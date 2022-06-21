@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>
    <div>
        <section>
            <form action="{{ route('product.update',$product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="">Product Name</label>
                                    <input type="text" v-model="product_name" name="title"  value="{{ $product->title }}" placeholder="Product Name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Product SKU</label>
                                    <input type="text" v-model="product_sku" value="{{ $product->sku }}" name="sku" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Description</label>
                                    <textarea v-model="description" id="" cols="30" rows="4" name="description" class="form-control">{{ $product->description }}</textarea>
                                </div>
                            </div>
                        </div>
        
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Media</h6>
                            </div>
                            <div class="card-body border">
                                <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions"></vue-dropzone>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Variants</h6>
                            </div>
                            <div class="card-body">
                                @foreach ($productVariants as $productVariant)
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Option</label>
                                            <select class="form-control" name="variant_option[]">
                                                @foreach ($variants as $variant)
                                                    <option value="{{ $variant->id }}" @isset($productVariant->variant_id){{ $variant->id == $productVariant->variant_id ? 'selected' : ''}}                                                    
                                                    @endisset>{{ $variant->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="">variant</label>
                                            <input type="text" name="variant_name[]" class="form-control" value="{{ $productVariant->variant }}">
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                            </div>
                            <div class="card-footer" v-if="product_variant.length < variants.length && product_variant.length < 3">
                                <button @click="newVariant" class="btn btn-primary">Add another option</button>
                            </div>
        
                            <div class="card-header text-uppercase">Preview</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <td>Variant</td>
                                            <td>Price</td>
                                            <td>Stock</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($productVariantPrices as $key=>$productVariantPrice)
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <input type="text" class="form-control" name="product_price[]" value="{{ $productVariantPrice->price }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="product_stock[]" value="{{ $productVariantPrice->stock }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-lg btn-primary">Update</button>
                <button type="button" class="btn btn-secondary btn-lg">Cancel</button>
            </form>
        </section>
    </div>
@endsection
