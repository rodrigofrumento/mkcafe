<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductPhotoController extends Controller
{
    /**
     * Remove a foto pelo ID, e só se ela for de um produto da loja do usuário.
     * (Antes recebia o caminho do arquivo no formulário e apagava qualquer
     * arquivo do disco público, de qualquer loja.)
     */
    public function destroy(Request $request, int $photo): RedirectResponse
    {
        $storeId = $request->user()->store?->id;

        $photo = ProductPhoto::whereHas('product', fn ($query) => $query->where('store_id', $storeId))
            ->findOrFail($photo);

        Storage::disk('public')->delete($photo->image);
        $photo->delete();

        flash('Imagem removida com sucesso!')->success();

        return redirect()->route('admin.products.edit', ['product' => $photo->product_id]);
    }
}
