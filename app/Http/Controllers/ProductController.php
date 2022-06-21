<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    { 
        $product_variants = ProductVariant::groupBy('variant')->get()->toArray(); 
        $varients = ProductVariant::all();   
        $products = DB::table('products')
        ->leftjoin('product_variants', 'products.id', '=', 'product_variants.product_id')
        ->leftjoin('product_variant_prices', 'products.id', '=', 'product_variant_prices.product_id') 
        ->select('products.id as id', 'products.title', 'products.sku', 'products.created_at', 'products.updated_at', 'products.description', 'product_variants.variant','product_variants.variant_id','product_variant_prices.product_variant_one', 'product_variant_prices.product_variant_two', 'product_variant_prices.product_variant_three','product_variant_prices.price', 'product_variant_prices.stock')
        ->groupBy('products.sku')
        ->orderBy('id', 'asc');
        
        if(isset($request->title) && $request->title != '') {
            $products = $products->where('products.title','LIKE', "%$request->title%");
        }

        if(isset($request->variant) && $request->variant != '') { 
            $products = $products->where('product_variants.variant','LIKE', "%$request->variant%");
        }

        if(isset($request->price_from) && $request->price_from != '' && isset($request->price_to) && $request->price_to != '') {
            $price_from = doubleval($request->price_from);
            $price_to = doubleval($request->price_to);
            $products = $products->where('product_variant_prices.price', '>=', $price_from)
                                    ->where('product_variant_prices.price', '<=', $price_to);
        }

        if(isset($request->date) && $request->date != '') {
            $products = $products->where('products.created_at', '<=' , $request->date.' 00:00:00');
        }
        $products = $products->paginate(5);          
         
        return view('products.index',compact('products','product_variants', 'varients'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        $product_variant_prices = ProductVariantPrice::all();
        $product_variant = ProductVariant::all();
        return view('products.create', compact('variants', 'product_variant_prices', 'product_variant'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    { 
        $request->validate([
            'title' => 'required',
            'sku' => 'required',
        ]);
        // dd($request->all());
        
        $product = new Product;
        $product->title = $request->title;
        $product->sku = $request->sku;
        $product->description = $request->description;
        $product->save();
        
        $last = DB::table('products')->latest()->first();

        $total_variants = $request->product_variant;
        
        foreach ($total_variants as $variant) { 
            if (isset($variant->tags) || isset($variant->option)){  
                $product_variants = new ProductVariant; 
                $product_variants->variant = $variant->tags;
                $product_variants->variant_id = $variant->option;
                $product_variants->product_id = $last->id;
                // dd($product_variants);
                $product_variants->save();
            }  

        } 

        $total_variants_prices = $request->product_variant_prices;
        foreach ($total_variants_prices as $variant) {
            if (isset($variant->stock) && isset($variant->price)){
                $product_variants = new ProductVariantPrice; 
                $product_variants->price = $variant->price;
                $product_variants->stock = $variant->stock;
                $product_variants->product_id = $last->id;
                $product_variants->product_variant_one = $variant->product_variant_one;
                $product_variants->product_variant_two = $variant->product_variant_two;
                $product_variants->product_variant_three = $variant->product_variant_three;
                $product_variants->save();
            }
        }

        $total_product_images = $request->product_image;
        foreach ($total_product_images as $image) {
            if (isset($image->file_path) && isset($image->thumbnail)){
                $product_image = new ProductImage; 
                $product_image->thumbnail = $image->thumbnail;
                $product_image->file_path = $image->file_path;
                $product_image->product_id = $last->id; 
                $product_image->save();
            }
        }

        $res = [];
        $res['status'] = 'success';
        $res['message'] = 'product added successfully'; 
        return Response::json($res);  
        
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('products.show',compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $variants = Variant::all();
        $productVariants = ProductVariant::where('product_id',$id)->get();
        $productVariantPrices = ProductVariantPrice::where('product_id',$id)->get();
        return view('products.edit', compact('variants', 'product','productVariants','productVariantPrices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'sku' => 'required',
        ]);
        $product = Product::find($id);
        $product->update($request->all());

        

        // $request->validate([
        //     'title' => 'required',
        //     'sku' => 'required',
        // ]);
        // // dd($request->all());
        // $product = new Product;
        // $product->title = $request->title;
        // $product->sku = $request->sku;
        // $product->description = $request->description;
        // $product->save();
        
        // $last = DB::table('products')->latest()->first();

        // $total_variants = $request->product_variant;
        // foreach ($total_variants as $variant) {
        //     if (isset($variant->tags) && isset($variant->option)){
        //         $product_variants = new ProductVariant; 
        //         $product_variants->variant = $variant->tags;
        //         $product_variants->varient_id = $variant->option;
        //         $product_variants->product_id = $last->id;
        //         $product_variants->save();
        //     }
        // } 



        $res = [];
        $res['status'] = 'success';
        $res['message'] = 'product updated successfully'; 
        return Response::json($res);  
     
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
    
        return redirect()->route('product.index')
                        ->with('success','Product deleted successfully');
    }
}
