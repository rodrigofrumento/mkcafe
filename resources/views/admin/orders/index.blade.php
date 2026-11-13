@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h2>Pedidos Recebidos</h2>
        <hr>
    </div>
    <div class="col-12">
        <div class="accordion" id="ordersAccordion">
            @forelse ($orders as $key => $order)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button @if($key !== 0) collapsed @endif" type="button"
                                data-bs-toggle="collapse" data-bs-target="#order{{ $order->id }}"
                                aria-expanded="{{ $key === 0 ? 'true' : 'false' }}" aria-controls="order{{ $order->id }}">
                            Pedido nº {{ $order->reference }}
                        </button>
                    </h2>
                    <div id="order{{ $order->id }}" class="accordion-collapse collapse @if($key === 0) show @endif" data-bs-parent="#ordersAccordion">
                        <div class="accordion-body">
                            <ul>
                                @foreach ($order->itemsForStore($store->id) as $item)
                                    <li>
                                        {{ $item['name'] }} | R$ {{ number_format($item['price'], 2, ',', '.') }}<br>
                                        Quantidade: {{ $item['amount'] }}<br>
                                        Total: R$ {{ number_format($item['price'] * $item['amount'], 2, ',', '.') }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-warning">Nenhum pedido recebido</div>
            @endforelse
        </div>
        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
