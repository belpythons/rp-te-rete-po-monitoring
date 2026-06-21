<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Procurement extends Model
{
    use HasFactory;

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
        'phase',
        'tanggal_masuk',
    ];

    /**
     * Attribute casting (removed date casts since all date columns are strings).
     */
    protected $casts = [];

    /**
     * Status constants for reference throughout the application.
     */
    const STATUS_PENDING          = 'Pending';
    const STATUS_DISETUJUI        = 'Disetujui';
    const STATUS_TIDAK_DISETUJUI  = 'Tidak Disetujui';

    /**
     * Phase constants for reference throughout the application.
     */
    const PHASE_RP   = 'RP';
    const PHASE_TE   = 'TE';
    const PHASE_RETE = 'RE-TE';
    const PHASE_PO   = 'PO';

    /**
     * Get all valid statuses.
     */
    public static function validStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_DISETUJUI,
            self::STATUS_TIDAK_DISETUJUI,
        ];
    }

    /**
     * Get all valid phases.
     */
    public static function validPhases(): array
    {
        return [
            self::PHASE_RP,
            self::PHASE_TE,
            self::PHASE_RETE,
            self::PHASE_PO,
        ];
    }

    /**
     * Parse date string into Carbon object.
     */
    public static function parseDateString($dateStr): ?\Carbon\Carbon
    {
        if (empty($dateStr)) {
            return null;
        }

        $replacements = [
            'Januari'   => 'January',
            'Februari'  => 'February',
            'Maret'     => 'March',
            'Mei'       => 'May',
            'Juni'      => 'June',
            'Juli'      => 'July',
            'Agustus'   => 'August',
            'Oktober'   => 'October',
            'Nopember'  => 'November',
            'Desember'  => 'December'
        ];

        $cleaned = str_ireplace(array_keys($replacements), array_values($replacements), $dateStr);
        $cleaned = str_ireplace('Thusrday', 'Thursday', $cleaned);

        try {
            return \Carbon\Carbon::parse($cleaned);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Mutator to parse and format date_created before saving to database.
     */
    public function setDateCreatedAttribute($value)
    {
        $this->attributes['date_created'] = $this->parseAndFormatDate($value);
    }

    public function setSendForApprovalGeneralDirectorAttribute($value)
    {
        $this->attributes['send_for_approval_general_director'] = $this->parseAndFormatDate($value);
    }

    public function setTeInAttribute($value)
    {
        $this->attributes['te_in'] = $this->parseAndFormatDate($value);
    }

    public function setTeOutAttribute($value)
    {
        $this->attributes['te_out'] = $this->parseAndFormatDate($value);
    }

    public function setReTeAttribute($value)
    {
        $this->attributes['re_te'] = $this->parseAndFormatDate($value);
    }

    public function setDeliveryAttribute($value)
    {
        $this->attributes['delivery'] = $this->parseAndFormatDate($value);
    }

    public function setTanggalMasukAttribute($value)
    {
        $this->attributes['tanggal_masuk'] = $this->parseAndFormatDate($value);
    }

    protected function parseAndFormatDate($value)
    {
        if (empty($value)) {
            return null;
        }

        $parsed = self::parseDateString($value);
        return $parsed ? $parsed->format('Y-m-d') : $value;
    }
}