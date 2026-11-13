@extends('layouts.app')
@section('content')
    <h1>Atualizar Produto</h1>
    <form action="{{route('admin.products.update', ['product' => $product->id])}}" method="post" enctype="multipart/form-data">
        @csrf
        @method("PUT")
        <div class="mb-3">
            <label>Nome Produto</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{$product->name}}">
            @error('name')
            <div class="invalid-feedback">
                {{$message}}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label>Descrição</label>
            <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{$product->description}}">
            @error('description')
            <div class="invalid-feedback">
                {{$message}}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label>Conteúdo</label>
            <textarea name="body" id="" cols="30" rows="10" class="form-control @error('body') is-invalid @enderror">{{$product->body}}</textarea>
            @error('body')
            <div class="invalid-feedback">
                {{$message}}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label>Preço</label>
            <input type="text" name="price" id="price" data-mask="money" inputmode="decimal" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', number_format($product->price, 2, ',', '.')) }}">
            @error('price')
            <div class="invalid-feedback">
                {{$message}}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label>Categorias</label>
            <select name="categories[]" class="form-select" multiple>
                @foreach ($categories as $category)
                    <option value="{{$category->id}}" 
                        @if($product->categories->contains($category)) selected @endif     
                    >{{$category->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Fotos do Produto</label>
            <input type="file" name="photos[]" class="form-control @error('photos.*') is-invalid @enderror" multiple>
            @error('photos.*')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
            @enderror
        </div>
        <div>
            <button type="submit" class="btn btn-lg btn-success">Atualizar Produto</button>
        </div>
    </form>
    <hr>
    <div class="row">
        @foreach ($product->photos as $photo)
            <div class="col-4 text-center">
                <img src="{{asset('storage/' . $photo->image)}}" alt="" class="img-fluid">
                <form action="{{ route('admin.photo.remove', ['photo' => $photo->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-lg btn-danger">Apagar Foto</button>
                </form>
            </div>
        @endforeach
    </div>
@endsection

