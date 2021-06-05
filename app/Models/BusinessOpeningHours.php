<?php

namespace App\Models;

use App\Models\Traits\HasOpenableHours;
use Illuminate\Database\Eloquent\Model;

class BusinessOpeningHours extends Model
{
    use HasOpenableHours;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $table = 'business_hours';
    protected $guarded = ['id'];
    protected $appends = ['open', 'close', 'status'];

    public function business()
    {
    	return $this->belongsTo(Business::class);
    }

    public function uriKey()
    {
        return $this->uuid;
    }
}
