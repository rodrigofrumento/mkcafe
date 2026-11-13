@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <form action="{{ route('admin.notifications.read.all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-lg btn-success">Marcar tudo como lido</button>
            </form>
            <hr>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Notificação</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($unreadNotifications as $n)
                <tr>
                    <td>{{ $n->data['message'] }}</td>
                    <td>{{ $n->created_at->locale('pt_BR')->diffForHumans() }}</td>
                    <td>
                        <form action="{{ route('admin.notifications.read', ['notification' => $n->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">Marcar como Lida</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">
                        <div class="alert alert-warning mb-0">Nenhuma notificação encontrada!</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
