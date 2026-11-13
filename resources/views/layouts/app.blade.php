<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Feirão da Fotografia</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
<nav class="navbar navbar-expand-lg bg-dark mb-4" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">Feirão!</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin"
                aria-controls="navbarAdmin" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarAdmin">
            @auth
                @if (auth()->user()->isOwner())
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('admin/orders*')) active @endif" href="{{ route('admin.orders.my') }}">Pedidos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('admin/stores*')) active @endif" href="{{ route('admin.stores.index') }}">Loja</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('admin/products*')) active @endif" href="{{ route('admin.products.index') }}">Produtos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(request()->is('admin/categories*')) active @endif" href="{{ route('admin.categories.index') }}">Categorias</a>
                        </li>
                    </ul>
                @else
                    <div class="me-auto"></div>
                @endif
                <ul class="navbar-nav">
                    @if (auth()->user()->isOwner())
                        <li class="nav-item">
                            <a href="{{ route('admin.notifications.index') }}" class="nav-link" aria-label="Notificações">
                                @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                                @if ($unread)
                                    <span class="badge text-bg-danger">{{ $unread }}</span>
                                @endif
                                <i class="bi bi-bell"></i>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <span class="nav-link">{{ auth()->user()->name }}</span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link">Sair</button>
                        </form>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>
<div class="container">
    @include('flash::message')
    @yield('content')
</div>
@yield('scripts')
</body>
</html>
