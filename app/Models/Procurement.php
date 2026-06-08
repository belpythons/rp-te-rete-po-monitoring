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
        'no',
        'rp_number',
        'description',
        'date_created',
        'send_for_approval_general_director',
        'buyer',
        'te_in',
        'te_out',
        're_te',
        'po',
        'vendor',
        'delivery',
        'so',
        'qc',
        'rr',
        'status',
    ];

    /**
     * Attribute casting (removed date casts since all date columns are strings).
     */
    protected $casts = [];

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