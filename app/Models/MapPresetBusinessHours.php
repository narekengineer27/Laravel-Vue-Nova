<?php

namespace App\Models;

use App\Models\Traits\HasOpenableHours;
use Illuminate\Database\Eloquent\Model;

class MapPresetBusinessHours extends Model
{
    use HasOpenableHours;

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function preset()
    {
        return $this->belongsTo(MapPreset::class);
    }

    public function setOpenPeriodMinsAttribute($value)
    {
        $this->attributes['open_period_mins'] = Business::minutesCnt($value);
    }

    public function setClosePeriodMinsAttribute($value)
    {
        $this->attributes['close_period_mins'] = Business::minutesCnt($value);
    }
}
