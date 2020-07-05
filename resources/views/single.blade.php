@extends('layouts.front')

@section('content')
    <div class="row">
        <div class="col-6">
            @if ($product->photos->count())
                <img src="{{asset('storage/' . $product->photos->first()->image)}}" alt="" class="card-img-top">
            @else    
                <img src="{{asset('assets/img/946x946.png')}}" alt="" class="card-img-top">
            @endif
            <div class="row" style="margin-top: 20px">
                @foreach($product->photos as $photo)
                    <div class="col-4">
                        <img src="{{asset('storage/' . $photo->image)}}" alt="" class="img-fluid">
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-6">
            <div>            
                <h2>{{$product->name}}</h2>
                <p>{{$product->description}}</p>
                <h3>R$ {{number_format($product->price, '2', ',', '.')}}</h3>
                <span>{{$product->store->name}}</span>
            </div>
            <div class="product-add">
                <form action="{{route('cart.add')}}" method="post">
                    @csrf
                    <input type="hidden" name="product[name]" value="{{$product->name}}">
                    <input type="hidden" name="product[price]" value="{{$product->price}}">
                    <input type="hidden" name="product[slug]" value="{{$product->slug}}">
                    <div class="form-group">
                        <hr>
                        <label>Quantidade</label>
                        <input type="number" name="product[amount]" class="form-control col-md-2" value="1">
                    </div>
                    <button class="btn btn-lg btn-danger">Comprar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
        <hr>
        {{$product->body}}
        </div>
    </div>
@endsection