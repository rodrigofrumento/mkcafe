<?php

namespace App\Models;

use App\Notifications\StoreReceiveNewOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Store extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = ['name', 'description', 'phone', 'mobile_phone', 'logo'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(UserOrder::class, 'order_store', 'store_id', 'order_id');
    }

    /**
     * @param  array<int>  $storeIds
     */
    public static function notifyOwners(array $storeIds): void
    {
        static::with('user')->whereIn('id', $storeIds)->get()
            ->pluck('user')
            ->filter()
            ->each->notify(new StoreReceiveNewOrder);
    }
}
