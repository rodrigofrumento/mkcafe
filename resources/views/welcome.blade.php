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
@endsection