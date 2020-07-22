<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Feirão da Fotografia!</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="margin-bottom: 30px">
        <a class="navbar-brand" href="{{route('home')}}">Feirão!</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          @auth
          <ul class="navbar-nav mr-auto">
            <li class="nav-item @if(request()->is('admin/orders*')) active @endif">
              <a class="nav-link" href="{{route('admin.orders.my')}}">Meus Pedidos</a>
              </li>
            <li class="nav-item @if(request()->is('admin/stores*')) active @endif">
            <a class="nav-link" href="{{route('admin.stores.index')}}">Loja <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item @if(request()->is('admin/products*')) active @endif">
              <a class="nav-link" href="{{route('admin.products.index')}}">Produtos</a>
            </li>
            <li class="nav-item @if(request()->is('admin/categories*')) active @endif">
              <a class="nav-link" href="{{route('admin.categories.index')}}">Categorias</a>
            </li>
          </ul>
          <div class="my-2 my-lg-0">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                <a class="nav-link" href="#" onclick="document.querySelector('form.logout').submit()">Sair </a>
                <form action="{{route('logout')}}" class="logout" method="POST" style="display:none">
                    @csrf
                </form>
                </li>
                <li class="nav-item">
                  <span class="nav-link">{{auth()->user()->name}}</span>
                </li>
              </ul>
          </div>
          @endauth
        </div>
      </nav>
    <div class="container">
        @include('flash::message')
        @yield('content')
    </div>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>