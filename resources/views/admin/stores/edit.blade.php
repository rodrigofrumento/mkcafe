@extends('layouts.app')
@section('content')
    <h1>Editar Loja</h1>
<form action="{{route('admin.stores.update', ['store'=>$store->id])}}" method="post" enctype="multipart/form-data">
    @csrf
    @method("PUT")
    <div class="mb-3">
    <div>
        <label>Nome Loja</label>
            <input type="text" name="name" class="form-control" value="{{$store->name}}">
    </div>
    <div class="mb-3">
        <label>Descrição</label>
            <input type="text" name="description" class="form-control" value="{{$store->description}}">
    </div>
    <div class="mb-3">
        <label>Telefone</label>
            <input type="text" name="phone" id="phone" data-mask="phone" class="form-control" value="{{$store->phone}}">
    </div>
    <div class="mb-3">
        <label>Celular/Whatsapp</label>
            <input type="text" name="mobile_phone" id="mobile_phone" data-mask="mobile" class="form-control" value="{{$store->mobile_phone}}">
    </div>
    <div class="mb-3">
        <p>
            <img src="{{asset('storage/'.$store->logo)}}" alt="">
        </p>
        <label>Logo</label>
        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror">
        @error('logo') 
            <div class="invalid-feedback">
                {{$message}}
            </div>
        @enderror
    </div>
    <div>
    <button type="submit" class="btn btn-lg btn-success">Atualizar Loja</button>
    </div>
    </form>
@endsection

