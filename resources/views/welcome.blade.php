@extends('layouts.front')

@section('content')
<div class="row">
    @foreach ($products as $product)
        <div class="col-md-4">
            <div class="card" style="width:98%">
                @if ($product->photos->count())
                    <img src="{{asset('storage/' . $product->photos->first()->image)}}" alt="" class="card-img-top">
                @else    
                    <img src="{{asset('assets/img/946x946.png')}}" alt="" class="card-img-top">
                @endif
                <div class="card-body">
                    <h2 class="card-title">{{$product->name}}</h2>
                    <p class="card-text">{{$product->description}}</p>
                    <h3>R$ {{number_format($product->price, '2', ',', '.')}}</h3>
                    <a href="{{route('product.single', ['slug' => $product->slug])}}" class="btn btn-success">ver mais</a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    <div class="col-md-12">
        <h2>Lojas Destaque</h2>
        <hr>
    </div>
    @foreach ($stores as $store)
    <div class="col-md-4">
        @if ($store->logo)
            <img src="{{asset('storage/' . $store->logo)}}" alt="{{$store->name}}" class="img-fluid">
        @else
            <img src="https://via.placeholder.com/600X300.png?text=logo" alt="loja sem logo..." class="img-fluid">       
        @endif
        <h3>{{$store->name}}</h3>
        <p>
            {{$store->description}}
        </p>
    </div> 
    @endforeach
</div>

@endsection