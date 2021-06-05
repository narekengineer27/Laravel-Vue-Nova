<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasUuid;

class BusinessPostImage extends Model
{
	use HasUuid;

    protected $fillable = ['path'];
    protected $hidden = ['uuid'];

    public function post()
    {
        return $this->belongsTo(BusinessPost::class);
    }

}
