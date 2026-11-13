<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserOrder extends Model
{
    public const STATUS_PENDING = 'pending';

    protected $fillable = ['reference', 'status', 'items'];

    protected function casts(): array
    {
        // JSON em vez de serialize()/unserialize(), que abre espaço para
        // injeção de objetos PHP ao ler dados do banco.
        return ['items' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'order_store', 'order_id', 'store_id');
    }

    /**
     * Itens do pedido que pertencem a uma loja específica.
     *
     * @return array<int, array<string, mixed>>
     */
    public function itemsForStore(int $storeId): array
    {
        return array_values(array_filter(
            $this->items ?? [],
            fn (array $item) => (int) $item['store_id'] === $storeId,
        ));
    }

    public function total(): float
    {
        return array_sum(array_map(
            fn (array $item) => $item['price'] * $item['amount'],
            $this->items ?? [],
        ));
    }
}
