<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPurchaseLimit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'inventory_item_id', 'daily_need', 'planning_days',
        'calculated_need', 'max_purchase', 'last_calculated_at', 'updated_at',
    ];

    protected $casts = [
        'daily_need'          => 'decimal:3',
        'calculated_need'     => 'decimal:3',
        'max_purchase'        => 'decimal:3',
        'last_calculated_at'  => 'datetime',
        'updated_at'          => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
