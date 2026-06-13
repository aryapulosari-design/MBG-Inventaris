<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'loggable_type', 'loggable_id', 'action',
        'old_values', 'new_values',
        'user_id', 'user_name', 'ip_address', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    // ─── Immutability enforcement (BR-07) ────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function () {
            throw new LogicException('Audit log tidak dapat diubah.');
        });

        static::deleting(function () {
            throw new LogicException('Audit log tidak dapat dihapus.');
        });
    }

    // ─── Factory method ──────────────────────────────────────────────

    public static function catat(
        Model $model,
        string $action,
        array $oldValues = [],
        array $newValues = []
    ): void {
        static::create([
            'loggable_type' => class_basename($model),
            'loggable_id'   => $model->id,
            'action'        => $action,
            'old_values'    => $oldValues ?: null,
            'new_values'    => $newValues ?: null,
            'user_id'       => auth()->id(),
            'user_name'     => auth()->user()?->name,
            'ip_address'    => request()->ip(),
            'user_agent'    => substr(request()->userAgent() ?? '', 0, 255),
        ]);
    }

    // ─── Relationships ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Accessors ───────────────────────────────────────────────────

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created'   => 'Dibuat',
            'updated'   => 'Diperbarui',
            'deleted'   => 'Dihapus',
            'stock_in'  => 'Stok Masuk',
            'stock_out' => 'Stok Keluar',
            default     => ucfirst($this->action),
        };
    }

    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created'   => 'success',
            'updated'   => 'info',
            'deleted'   => 'danger',
            'stock_in'  => 'primary',
            'stock_out' => 'warning',
            default     => 'secondary',
        };
    }
}
