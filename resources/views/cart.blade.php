@extends('layouts.front')

@section('content')
<div class="row">
    <div class="col-12">
        <h2>Carrinho de Compras</h2>
        <hr>
    </div>
    <div class="col-12">
        @if ($cart)
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach ($cart as $item)
                        @php
                            $subtotal = $item['price'] * $item['amount'];
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                            <td>{{ $item['amount'] }}</td>
                            <td>R$ {{ number_format($subtotal, 2, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('cart.remove', ['slug' => $item['slug']]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Remover</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3">Total:</td>
                        <td colspan="2">R$ {{ number_format($total, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <div class="d-flex justify-content-between">
                <form action="{{ route('cart.cancel') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-lg btn-danger">Cancelar Compra</button>
                </form>
                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-lg btn-success">Concluir Compra</button>
                </form>
            </div>
            @guest
                <p class="text-muted text-end mt-2">Você precisará entrar na sua conta para concluir.</p>
            @endguest
        @else
            <div class="alert alert-warning">Carrinho Vazio</div>
        @endif
    </div>
</div>
@endsection
