<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use ScoutElastic\Searchable;

class BusinessReview extends Model
{
    use HasUuid, Searchable;

    protected $guarded = ['id'];
    protected $with    = ['keywords'];
    protected $hidden  = ['uuid'];

    protected $indexConfigurator = \App\Elastic\Configurators\BusinessReview::class;

    public function toSearchableArray()
    {
        return [
            'id'            => $this->uuid,
            'business_name' => $this->business->name,
            'business_id'   => $this->business->uuid,
            'user_id'       => $this->user_id,
            'type'          => 'review',
            'cover_image'        => $this->cover_image,
            'location'      => [
                'lat' => $this->business->lat,
                'lon' => $this->business->lng
            ],
            'score'         => $this->score,
            'comment'       => $this->comment,
            'meta'          => $this->meta,
            'hours'         => $this->business->hours
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function keywords()
    {
        return
            $this
                ->hasMany(BusinessReviewKeyword::class)
                ->orderBy('relevance', 'DESC')
            ;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return
            $this
                ->hasMany(BusinessReviewImage::class)
            ;
    }

    /**
     * @return string
     */
    public function getKeywordsListAttribute()
    {
        return
            $this->hasMany(BusinessReviewKeyword::class)->pluck('keyword')->implode(', ');
    }
}
