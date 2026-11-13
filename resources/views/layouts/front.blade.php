<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Feirão da Fotografia</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('stylesheets')
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary mb-5">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">Feirão da Fotografia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFront"
                aria-controls="navbarFront" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarFront">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link @if(request()->is('/')) active @endif" href="{{ route('home') }}">Home</a>
                </li>
                @foreach ($categories as $category)
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('category/'.$category->slug)) active @endif"
                           href="{{ route('category.single', ['slug' => $category->slug]) }}">{{ $category->name }}</a>
                    </li>
                @endforeach
            </ul>
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item">
                        <span class="nav-link">{{ auth()->user()->name }}</span>
                    </li>
                    @if (auth()->user()->isOwner())
                        <li class="nav-item">
                            <a href="{{ route('admin.stores.index') }}" class="nav-link">Painel</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('user.orders') }}" class="nav-link @if(request()->is('my-orders')) active @endif">Meus Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link">Sair</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">Entrar</a></li>
                    <li class="nav-item"><a href="{{ route('register') }}" class="nav-link">Cadastrar</a></li>
                @endauth
                <li class="nav-item">
                    <a href="{{ route('cart.index') }}" class="nav-link" aria-label="Carrinho">
                        @if (session()->has('cart'))
                            <span class="badge text-bg-danger">{{ count(session('cart')) }}</span>
                        @endif
                        <i class="bi bi-cart"></i>
                    </a>
                </li>
            </ul>
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
