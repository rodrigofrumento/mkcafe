@extends('layouts.app')
@section('content')
    <h1>Editar Loja</h1>
<form action="{{route('admin.stores.update', ['store'=>$store->id])}}" method="post" enctype="multipart/form-data">
    @csrf
    @method("PUT")
    <div class="form-group">
    <div>
        <label>Nome Loja</label>
            <input type="text" name="name" class="form-control" value="{{$store->name}}">
    </div>
    <div class="form-group">
        <label>Descrição</label>
            <input type="text" name="description" class="form-control" value="{{$store->description}}">
    </div>
    <div class="form-group">
        <label>Telefone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{$store->phone}}">
    </div>
    <div class="form-group">
        <label>Celular/Whatsapp</label>
            <input type="text" name="mobile_phone" id="mobile_phone" class="form-control" value="{{$store->mobile_phone}}">
    </div>
    <div class="form-group">
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

@section('scripts')
    <script>
        let imPhone = new Inputmask("(99) 9999-9999");
        imPhone.mask(document.getElementById("phone"));
        let imMobilePhone = new Inputmask("(99) 9999-99999");
        imMobilePhone.mask(document.getElementById("mobile_phone"));
    </script>
@endsection