@extends('layouts.front')

@section('content')
<div class="row">
    <div class="col-12">
        <h2>Meus Pedidos</h2>
        <hr>
    </div>
    <div class="col-12">
        <div class="accordion" id="ordersAccordion">
            @forelse ($userOrders as $key => $order)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button @if($key !== 0) collapsed @endif" type="button"
                                data-bs-toggle="collapse" data-bs-target="#order{{ $order->id }}"
                                aria-expanded="{{ $key === 0 ? 'true' : 'false' }}" aria-controls="order{{ $order->id }}">
                            Pedido nº {{ $order->reference }}
                            <span class="badge text-bg-secondary ms-2">{{ $order->status === 'pending' ? 'Pendente' : $order->status }}</span>
                        </button>
                    </h2>
                    <div id="order{{ $order->id }}" class="accordion-collapse collapse @if($key === 0) show @endif" data-bs-parent="#ordersAccordion">
                        <div class="accordion-body">
                            <ul>
                                @foreach ($order->items as $item)
                                    <li>
                                        {{ $item['name'] }} | R$ {{ number_format($item['price'], 2, ',', '.') }}<br>
                                        Quantidade: {{ $item['amount'] }}<br>
                                        Total: R$ {{ number_format($item['price'] * $item['amount'], 2, ',', '.') }}
                                    </li>
                                @endforeach
                            </ul>
                            <strong>Total do pedido: R$ {{ number_format($order->total(), 2, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-warning">Nenhum pedido realizado</div>
            @endforelse
        </div>
        <div class="mt-3">
            {{ $userOrders->links() }}
        </div>
    </div>
</div>
@endsection
