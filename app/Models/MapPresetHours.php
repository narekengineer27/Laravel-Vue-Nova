<?php

namespace App\Models;

use App\Models\Traits\HasOpenableHours;
use Illuminate\Database\Eloquent\Model;

class MapPresetHours extends Model
{
    use HasOpenableHours;

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function preset() {
        return $this->belongsTo(MapPreset::class);
    }

}
