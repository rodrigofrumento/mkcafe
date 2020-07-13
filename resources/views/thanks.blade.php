@extends('layouts.front')
@section('content')
    <h2 class="alert alert-success">Seu Pedido foi processado</h2>
    <h3>Código do pedido: {{request()->get('order')}} </h3>
@endsection