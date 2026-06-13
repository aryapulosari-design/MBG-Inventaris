<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'inventory_item_id', 'type', 'quantity', 'unit_price',
        'stock_before', 'stock_after', 'reason', 'reference_no',
        'supplier', 'notes', 'transacted_at', 'created_by', 'created_at',
    ];

    protected $casts = [
        'quantity'      => 'decimal:3',
        'unit_price'    => 'decimal:2',
        'stock_before'  => 'decimal:3',
        'stock_after'   => 'decimal:3',
        'transacted_at' => 'datetime',
        'created_at'    => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'purchase'   => 'Pembelian dari Supplier',
            'cooking'    => 'Digunakan Memasak',
            'waste'      => 'Terbuang/Rusak',
            'adjustment' => 'Penyesuaian/Koreksi',
            'return'     => 'Retur ke Supplier',
            'other'      => 'Lainnya',
            default      => $this->reason,
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'in' ? 'Stok Masuk' : 'Stok Keluar';
    }

    public function getTypeBadgeAttribute(): string
    {
        return $this->type === 'in' ? 'success' : 'danger';
    }

    public function getTotalNilaiAttribute(): float
    {
        return (float) $this->quantity * (float) ($this->unit_price ?? 0);
    }
}
