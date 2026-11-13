<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Dados de demonstração. Senha de todos os usuários: "password".
     */
    public function run(): void
    {
        $categories = collect(['Câmeras', 'Lentes', 'Acessórios', 'Iluminação'])
            ->map(fn (string $name) => Category::create(['name' => $name]));

        $owner = User::factory()->owner()->create([
            'name' => 'Dono da Loja',
            'email' => 'loja@example.com',
        ]);

        User::factory()->create([
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
        ]);

        $stores = collect([Store::factory()->for($owner)->create(['name' => 'Foto Center'])])
            ->merge(Store::factory()->count(2)->create());

        $stores->each(function (Store $store) use ($categories) {
            Product::factory()->count(4)->for($store)->create()
                ->each(fn (Product $product) => $product->categories()->attach(
                    $categories->random(2)->pluck('id')
                ));
        });
    }
}
