<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use ScoutElastic\Searchable;

class BusinessPost extends Model
{
    use HasUuid, Searchable;

    protected $fillable = ['business_id', 'user_id', 'expire_date', 'text', 'meta', 'title'];

    protected $casts = [
        'expire_date'  => 'date'
    ];

    protected $hidden = ['uuid'];

    protected $indexConfigurator = \App\Elastic\Configurators\BusinessPost::class;

    public function toSearchableArray()
    {
        return [
            'id'            => $this->uuid,
            'business_name' => $this->business->name,
            'business_id'   => $this->business->uuid,
            'user_id'       => $this->user_id,
            'title'         => $this->title,
            'type'          => 'post',
            'cover_photo'        => $this->cover_photo,
            'location'      => [
                'lat' => $this->business->lat,
                'lon' => $this->business->lng
            ],
            'text'          => $this->text,
            'meta'          => $this->meta,
            'expire_date'   => isset($this->expire_date) ?? $this->expire_date,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(BusinessPostImage::class);
    }
}
