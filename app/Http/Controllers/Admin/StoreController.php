<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequest;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StoreController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('user.has.store', only: ['create', 'store'])];
    }

    public function index(Request $request): View
    {
        $store = $request->user()->store;

        return view('admin.stores.index', compact('store'));
    }

    public function create(): View
    {
        return view('admin.stores.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logo', 'public');
        }

        $request->user()->store()->create($data);

        flash('Loja Criada com Sucesso!')->success();

        return redirect()->route('admin.stores.index');
    }

    public function edit(Request $request, int $store): View
    {
        $store = $this->ownStore($request, $store);

        return view('admin.stores.edit', compact('store'));
    }

    public function update(StoreRequest $request, int $store): RedirectResponse
    {
        $store = $this->ownStore($request, $store);
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }
            $data['logo'] = $request->file('logo')->store('logo', 'public');
        }

        $store->update($data);

        flash('Loja Atualizada com Sucesso!')->success();

        return redirect()->route('admin.stores.index');
    }

    public function destroy(Request $request, int $store): RedirectResponse
    {
        $store = $this->ownStore($request, $store);

        if ($store->logo) {
            Storage::disk('public')->delete($store->logo);
        }
        $store->delete();

        flash('Loja Removida com Sucesso!')->success();

        return redirect()->route('admin.stores.index');
    }

    /** Só a loja do próprio usuário; qualquer outro ID responde 404. */
    private function ownStore(Request $request, int $id): Store
    {
        return Store::where('user_id', $request->user()->id)->findOrFail($id);
    }
}
