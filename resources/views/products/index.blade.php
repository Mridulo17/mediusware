@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{ route('product.index') }}" method="GET" class="card-header">
            @csrf
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">                         
                        @foreach($product_variants as $variant)  
                            <option value="{{ $variant['variant'] }}"> {{ $variant['variant'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                        @foreach($products as $key => $product_detail)
                            <tr> 

                                <td>{{ $product_detail->id }}</td>
                                <td>{{ $product_detail->title }} <br> Created at : {{ Carbon\Carbon::parse($product_detail->created_at)->format('d-M-Y') }}</td>
                                <td>{{ $product_detail->description }}</td>

                                <td>
                                    <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">
        
                                        <dt class="col-sm-3 pb-0"> 
                                            @php
                                              $vari = []; 
                                                foreach($varients as $varient) { 
                                                    if ( $varient->product_id == $product_detail->id ) { 
                                                        array_push($vari, $variant['variant']); 
                                                    }
                                                } 
                                                if ($vari == '' || !isset($vari)) {
                                                    foreach($varients as $variant) {
                                                        if ( $varient->product_id == $product_detail->id ) {
                                                            $vari = $variant->variant;
                                                        }
                                                    }
                                                } 
                                            @endphp
                                            @if (isset($vari))
                                                @foreach ($vari as $key => $value)
                                                    {{ $value }}
                                                @endforeach
                                            @endif
                                            {{-- SM/ Red/ V-Nick --}}
                                        </dt>
                                        <dd class="col-sm-9">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-4 pb-0">Price : {{ number_format(200,2) }}</dt>
                                                <dd class="col-sm-8 pb-0">InStock : {{ number_format(50,2) }}</dd>
                                            </dl>
                                        </dd>
                                    </dl>
                                    <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                                </td>

                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('product.edit',$product_detail->id) }}" class="btn btn-success">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{($products->currentpage()-1)*$products->perpage()+1}} to {{$products->currentpage()*$products->perpage()}}
                        of  {{$products->total()}} entries</p>
                </div>
                <div class="col-md-6">
                    {!! $products->appends(['sort' => 'id'])->links() !!}
                </div>
                <div class="col-md-2">

                </div>
            </div>
        </div>
    </div>

@endsection
