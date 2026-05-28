<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procurement extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'procurements';

    /**
     * Mass-assignable attributes.
     */
    protected $fillable = [
        'kode_pengadaan',
        'nama_barang',
        'vendor',
        'tanggal_te',
        'tanggal_rete',
        'tanggal_po',
        'tanggal_in',
        'tanggal_out',
        'status',
        'quantity',
        'departemen',
        'keterangan',
        'hasil_evaluasi',
        'catatan',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'tanggal_te'   => 'date',
        'tanggal_rete' => 'date',
        'tanggal_po'   => 'date',
        'tanggal_in'   => 'date',
        'tanggal_out'  => 'date',
    ];

    /**
     * Boot method — register model event for auto-computing status.
     */
    protected static function booted(): void
    {
        static::saving(function (Procurement $procurement) {
            if ($procurement->isDirty('status')) {
                $procurement->syncDatesWithStatus($procurement->status);
            } else {
                $procurement->status = $procurement->computeStatus();
            }
        });
    }

    /**
     * Synchronize date fields automatically based on target status.
     */
    public function syncDatesWithStatus(string $status): void
    {
        $now = now();

        if ($status === self::STATUS_RP) {
            $this->tanggal_te = null;
            $this->tanggal_rete = null;
            $this->tanggal_po = null;
            $this->tanggal_out = null;
            if (is_null($this->tanggal_in)) {
                $this->tanggal_in = $now;
            }
        } elseif ($status === self::STATUS_TE) {
            if (is_null($this->tanggal_in)) {
                $this->tanggal_in = $now;
            }
            if (is_null($this->tanggal_te)) {
                $this->tanggal_te = $now;
            }
            $this->tanggal_rete = null;
            $this->tanggal_po = null;
        } elseif ($status === self::STATUS_RETE) {
            if (is_null($this->tanggal_in)) {
                $this->tanggal_in = $now;
            }
            if (is_null($this->tanggal_te)) {
                $this->tanggal_te = $now;
            }
            if (is_null($this->tanggal_rete)) {
                $this->tanggal_rete = $now;
            }
            $this->tanggal_po = null;
        } elseif ($status === self::STATUS_PO) {
            if (is_null($this->tanggal_in)) {
                $this->tanggal_in = $now;
            }
            if (is_null($this->tanggal_te)) {
                $this->tanggal_te = $now;
            }
            if (is_null($this->tanggal_rete)) {
                $this->tanggal_rete = $now;
            }
            if (is_null($this->tanggal_po)) {
                $this->tanggal_po = $now;
            }
        }
    }

    /**
     * Compute the procurement status based on which date fields are filled.
     */
    public function computeStatus(): string
    {
        if (!is_null($this->tanggal_po)) {
            return 'PO';
        }

        if (!is_null($this->tanggal_rete)) {
            return 'RE-TE';
        }

        if (!is_null($this->tanggal_te)) {
            return 'TE';
        }

        return 'RP';
    }

    /**
     * Get the active/latest phase date.
     */
    public function getTanggalAttribute()
    {
        return $this->tanggal_po 
            ?? $this->tanggal_rete 
            ?? $this->tanggal_te 
            ?? $this->tanggal_in 
            ?? $this->created_at;
    }

    /**
     * Status constants for reference throughout the application.
     */
    const STATUS_RP   = 'RP';
    const STATUS_TE   = 'TE';
    const STATUS_RETE = 'RE-TE';
    const STATUS_PO   = 'PO';

    /**
     * Get all valid statuses.
     */
    public static function validStatuses(): array
    {
        return [
            self::STATUS_RP,
            self::STATUS_TE,
            self::STATUS_RETE,
            self::STATUS_PO,
        ];
    }
}