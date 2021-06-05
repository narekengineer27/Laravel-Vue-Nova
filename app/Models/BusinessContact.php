<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessContact extends Model
{
    protected $table = 'business_contacts';

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

}
