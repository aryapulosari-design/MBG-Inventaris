<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku', 'name', 'category', 'supplier', 'stock', 'unit',
        'reorder_point', 'max_stock', 'unit_price', 'currency',
        'status', 'last_restocked', 'notes',
        // Tidak termasuk: 'low_stock_alerted_at', 'created_by' — diset manual di service
    ];

    protected $casts = [
        'stock'           => 'decimal:3',
        'reorder_point'   => 'decimal:3',
        'max_stock'       => 'decimal:3',
        'unit_price'      => 'decimal:2',
        'last_restocked'  => 'datetime',
        'low_stock_alerted_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    public function transactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function purchaseLimit(): HasOne
    {
        return $this->hasOne(StockPurchaseLimit::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'loggable_id')
                    ->where('loggable_type', 'InventoryItem');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<', 'reorder_point');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', 0);
    }

    // ─── Accessors ───────────────────────────────────────────────────

    public function getNilaiStokAttribute(): float
    {
        return (float) $this->stock * (float) $this->unit_price;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock < $this->reorder_point;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->stock <= 0;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active'       => 'success',
            'backordered'  => 'warning',
            'discontinued' => 'secondary',
            default        => 'secondary',
        };
    }

    public function getStockColorAttribute(): string
    {
        if ($this->stock <= 0) return 'danger';
        if ($this->stock < $this->reorder_point) return 'warning';
        return 'success';
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public function hasTransactions(): bool
    {
        return $this->transactions()->exists();
    }

    public function canBeDeleted(): bool
    {
        return !$this->hasTransactions();
    }
}
