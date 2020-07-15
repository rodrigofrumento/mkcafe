@extends('layouts.front')

@section('content')
    <div class="row front">
        <div class="col-12">
            <h2>{{$store->name}}</h2>
            <p>{{$store->description}}</p>
            <p><strong>Contatos: <span><i class="fa fa-phone"></i> {{$store->phone}} | <i class="fa fa-mobile"></i> {{$store->mobile_phone}}</span></strong></p>
            <hr>
        </div>
        @forelse ($store->products as $key => $product)
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
            @if(($key+1)% 3 == 0)</div><div class="row front"> @endif
        @empty
                <div class="col-12">
                <h3 class="alert alert-warning">Nenhum produto encontrado nesta Loja</h3>
                </div>
        @endforelse
    </div>
@endsection