@extends('layouts.app')
@section('content')
<a href="#" class="btn btn-lg btn-success">Marcar tudo como lido</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Notificação</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($unreadNotifications as $n)
            <tr>
                <td>{{$n->data->message}}</td>
                <td>{{$n->createdAt}}</td>
                <td>
                    <div class="btn-group">
                        <a href="#" class="btn btn-sm btn-primary">Marcar como Lida</a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection