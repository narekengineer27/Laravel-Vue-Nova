<?php

namespace App\Models;

use App\Models\Traits\HasOpenableHours;
use App\Models\Traits\WithRelationsTrait;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use ScoutElastic\Searchable;

class Business extends Model
{
    use Searchable, HasUuid, WithRelationsTrait, HasOpenableHours;

    const LIMIT = 10000; // needs to be optimized

    protected $guarded = [];

    protected $hidden  = ['internal_score', 'opening_hours_info'];

    protected $with = ['bookmark'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * @var \App\Elastic\Configurators\Business
     */
    protected $indexConfigurator = \App\Elastic\Configurators\Business::class;

    /**
     * @var array
     */
    protected $searchRules = [
        \App\Elastic\Rules\BusinessSearchRule::class,
    ];

    /**
     * On boot actions, attach default behaviour
     */
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($business) {
            $business->updateInternalScore();
            $business->updateScore();
        });
        static::deleting(function ($business) {
            $business->categories()->sync([]);
        });
    }

    /**
     * ElasticSearch search rules boosting
     */
    const boostOpened    = 5.5;
    const boostNameMatch = 1.0;
    const fuzziness      = 5;
    const OPEN_HOUR      = 'open';
    const BUSINESS_HOUR  = 'business';

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'id'              => [
                'type'  => 'integer',
                'index' => 'true',
            ],
            'name'            => [
                'type'   => 'text',
                'fields' => [
                    'english' => [
                        'type'     => 'text',
                        'analyzer' => 'english',
                    ],
                    'synonym' => [
                        'type'     => 'text',
                        'analyzer' => 'synonym_analyzer',
                        'index'    => 'true',
                    ],
                ],
            ],
            'suggest'         => [
                'type' => 'completion',
            ],
            'exact_name'      => [
                'type'     => 'text',
                'analyzer' => 'substring_analyzer',
                'index'    => 'true',
            ],
            'categories'      => [
                'type'       => 'nested',
                'properties' => [
                    'id'   => [
                        'type'  => 'integer',
                        'index' => 'false',
                    ],
                    'name' => [
                        'type'     => 'text',
                        'index'    => 'true',
                        'analyzer' => 'whitespace_analyzer',
                    ],
                ],
            ],
            'reviews'      => [
                'type'       => 'nested',
                'properties' => [
                    'id'   => [
                        'type'  => 'integer',
                        'index' => 'false',
                    ],
                ],
            ],
            'posts'      => [
                'type'       => 'nested',
                'properties' => [
                    'id'   => [
                        'type'  => 'integer',
                        'index' => 'false',
                    ],
                ],
            ],
            'location'        => [
                'type'  => 'geo_point',
                'index' => 'true',
            ],
            'total_reviews'   => [
                'type' => 'long',
            ],
            'score'           => [
                'type'  => 'integer',
            ],
            'internal_score'  => [
                'type' => 'integer',
            ],
            'total_posts'     => [
                'type' => 'long',
            ],
            'is_open' => [
                'type'  => 'boolean',
                'index' => 'true',
            ],
            'open_description' => [
                'type'  => 'text',
                'index' => 'false',
            ],
            'cover_photo' => [
                'type'  => 'text',
                'index' => 'false',
            ],
            'avatar'          => [
                'type'  => 'text',
                'index' => 'false',
            ],
        ],
    ];

    /**
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id'                  => $this->id,
            'uuid'                => $this->uuid,
            'name'                => $this->name,
            'suggest'             => $this->name,
            'exact_name'          => $this->name,
            'location'            => [
                'lat' => $this->lat,
                'lon' => $this->lng,
            ],
            'total_reviews'       => $this->total_reviews,
            'total_posts'         => $this->total_posts,
            'categories'          => $this->categories,
            'reviews'             => $this->reviews,
            'posts'               => $this->posts,
            'optional_attributes' => $this->optionalAttributes,
            'internal_score'      => $this->internal_score,
            'score'             => $this->score,
            'cover_photo'     => $this->cover_photo,
            'is_open'           => (bool) $this->is_open,
            'open_description' => $this->open_description,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function withRequiredForUpdate()
    {
        return (new static )->newQuery()
            ->with('categories')
            ->withCount('reviews', 'posts');
    }


    public function bookmark()
    {
        return $this->hasMany(Bookmark::class)->where('user_id', auth()->id() ?? null);
    }

    /**
     * @return int
     */
    public static function currentMinutes(): int
    {
        return intval(ceil((strtotime(now()) - strtotime("12:00am")) / 60));
    }

    /**
     * @return int
     */
    public function getTotalReviewsAttribute()
    {
        return $this->reviews->count();
    }

    /**
     * @return int
     */
    public function getTotalAttributesAttribute()
    {
        return isset($this->relations['attributes']) ? count($this->relations['attributes']) : 0;
    }

    /**
     * @return int
     */
    public function getTotalEmailAttributesAttribute()
    {
        return isset($this->relations['totalEmailAttributes']) ? count($this->relations['totalEmailAttributes']) : 0;
    }

    /**
     * @return array
     */
    public function getLocationAttribute()
    {
        return [$this->lng, $this->lat];
    }

    /**
     * @return void
     */
    private function updateScore()
    {
        $countReviews = $this->total_reviews;
        $avgReview    = $this->reviews_avg_code;

        if ($countReviews === 0) {
            $score = 80;
        } elseif ($countReviews < 5) {
            $score = ($avgReview / $countReviews * 0.3 + 0.5) * 100;
        } else {
            $score = $avgReview / $countReviews * 100;
        }

        $score = round($score);

        $this->score = $score;
    }

    /**
     * @return void
     */
    private function updateInternalScore()
    {
        $score = 0;
        if ($this->reviews_exists) {
            $score += 20;
        }
        if ($this->posts_exists) {
            $score += 20;
        }
        if ($this->categories_exists) {
            $score += 20;
        }
        if ($this->addyAttributes_exists) {
            $score += 20;
        }
        if ($this->attributes_count > 1) {
            $score += 20;
        }

        $this->internal_score = $score;
    }

    public function updateScores()
    {
        $this->updateInternalScore();
        $this->updateScore();
        $this->save();
    }

    public function getTotalPostsAttribute()
    {
        return $this->posts->count();
    }

    public function getOpeningHoursInfoAttribute($v)
    {
        if ($v) {
            return json_decode($v, true);
        }
        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this
            ->belongsToMany(Category::class, 'business_category')
            ->withPivot(['relevance'])
            ->withTimestamps()
            ->orderBy('relevance', 'DESC');
    }
    
    public function contacts()
    {
        return $this->hasMany(BusinessContact::class);
    }

    public function categoriesExists()
    {
        return $this->hasManyExists(BusinessCategory::class);
    }

    public function getCategoriesExistsAttribute()
    {
        return $this->getExistsAttributeValue('categoriesExists');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function keywords()
    {
        return $this->hasMany(BusinessKeyword::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributes()
    {
        return $this->hasMany(BusinessAttribute::class);
    }

    public function getAttributesCountAttribute($count)
    {
        if ($count === null) {
            $count = $this->attributes()->count();
        }

        return $count;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function totalEmailAttributes()
    {
        return
        $this->hasMany(BusinessAttribute::class)->where('key', 'email');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addyAttributes()
    {
        return $this->hasMany(BusinessAttribute::class)->where('key', 'addy');
    }

    public function addyAttributesExists()
    {
        return $this->hasManyExists(BusinessAttribute::class)->where('key', 'addy');
    }

    public function getAddyAttributesExistsAttribute()
    {
        return $this->getExistsAttributeValue('addyAttributesExists');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(BusinessReview::class)
                ->whereNotNull('comment')
                ->latest();
    }

    public function reviewsAvgCode()
    {
        return $this->hasManyAvg('score', BusinessReview::class);
    }

    public function getReviewsAvgCodeAttribute()
    {
        return $this->getAvgAttributeValue('reviewsAvgCode');
    }

    public function reviewsExists()
    {
        return $this->hasManyExists(BusinessReview::class);
    }

    public function getReviewsExistsAttribute()
    {
        return $this->getExistsAttributeValue('reviewsExists');
    }

    public function postImages()
    {
        return $this->hasMany(BusinessPost::class)->with('images.labels');
    }

    public function images()
    {
        return $this->hasManyThrough(BusinessPostImage::class, BusinessPost::class);
    }

    public function createReview($data)
    {
        return $this->reviews()->create($data);
    }

    public function posts()
    {
        return $this->hasMany(BusinessPost::class);
    }

    public function postsExists()
    {
        return $this->hasManyExists(BusinessPost::class);
    }

    public function getPostsExistsAttribute()
    {
        return $this->getExistsAttributeValue('postsExists');
    }

    public function users()
    {
        return $this
            ->belongsToMany(User::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function optionalAttributes()
    {
        return $this->belongsToMany(OptionalAttribute::class)
            ->withPivot(['description'])
            ->withTimestamps();
    }

    public function setOpenPeriodMinsAttribute($value)
    {
        $this->attributes['open_period_mins'] = Business::minutesCnt($value);
    }

    public function setClosePeriodMinsAttribute($value)
    {
        $this->attributes['close_period_mins'] = Business::minutesCnt($value);
    }

    public function getLimit()
    {
        return static::LIMIT;
    }

    public function topKeywords()
    {
        $business_id = $this->id;

        $sql = "
			(SELECT COUNT(*) cnt, keyword
				FROM business_review_keywords LEFT JOIN business_reviews ON business_reviews.id=business_review_keywords.business_review_id
				WHERE business_id=5792
				GROUP BY keyword
			) a
		";
    
        $keywords = DB::table(
            DB::raw(
                $sql
            )
    
        )
            ->whereRaw('cnt >=2')
            ->orderByRaw('cnt DESC LIMIT 10')
            ->get();

        return $keywords;
    }

    public function getTopics()
    {
        $bizID = $this->id;
        $sql = "
            SELECT keyword FROM business_review_keywords
                WHERE business_review_id IN (SELECT id FROM business_reviews WHERE business_id=$bizID)
                AND ( keyword NOT LIKE '% %')
                GROUP BY keyword
        ";
        $kws = DB::select($sql);
        $kws = self::__toAssoc($kws);

        $stories = [];
        $genStory = [];

        $MIN_STORY_LEN = 3;

        foreach ($kws as $kw) {
            $kw = strtolower($kw['keyword']);
            $sql = "
                    SELECT keyword, id FROM business_review_keywords
                        WHERE business_review_id IN (SELECT id FROM business_reviews WHERE business_id=$bizID)
                        AND (
                            keyword LIKE '$kw %'
                            OR
                            keyword LIKE '% $kw'
                            OR
                            keyword LIKE '% $kw %'
                        )
            ";
            $rtn = DB::select($sql);
            $rtn = self::__toAssoc($rtn);

            $dat = array();
            foreach ($rtn as $r) {
                $r['keyword'] = strtolower($r['keyword']);
                if (!isset($dat[$r['keyword']])) {
                    $dat[$r['keyword']] = array(
                        'keyword'=>$r['keyword'],
                        'cnt'=>0,
                        'ids'=>[],
                    );
                }
                $dat[$r['keyword']]['cnt']++;
                $dat[$r['keyword']]['ids'][]= $r['id'];
            }

            if (count($dat) >= $MIN_STORY_LEN) {
                $stories[$kw] = $dat;
            }
        }

        $sort = array();
        foreach ($stories as $k=>$phrases) {
            $cnt = 0;
            foreach ($phrases as $p) {
                $cnt += $p['cnt'];
            }
            $sort[]= $cnt;
        }
        array_multisort($sort, SORT_DESC, $stories);

        $frtn = array();
        foreach ($stories as $storyDat) {
            $maxCnt = 0;
            $maxKw = '';
            $ids = [];
            foreach ($storyDat as $kw=>$kwd) {
                if ($kwd['cnt'] > $maxCnt) {
                    $maxCnt = $kwd['cnt'];
                    $maxKw = $kw;
                }
                $ids = array_merge($ids, $kwd['ids']);
            }
            if ($maxCnt < 2) {
                continue;
            }

            $ids = implode(',', $ids);
            $sql = "
                SELECT score FROM business_reviews
                WHERE
                id IN (SELECT business_review_id FROM business_review_keywords WHERE id IN ($ids))
            ";
            $rtn = DB::select($sql);
            $rtn = self::__toAssoc($rtn);
            $scores = 0;
            foreach ($rtn as $r) {
                $r['score'] += 20;
                if ($r['score'] > 100) {
                    $r['score'] = 100;
                }
                $scores += $r['score'];
            }
            $score = round($scores/count($rtn));

            $sort = array();
            foreach ($storyDat as $s) {
                $sort[]= $s['cnt'];
            }
            array_multisort($sort, SORT_DESC, $storyDat);

            $frtn[]= array(
                "title"=>$maxKw,
                //"phrases"=>$phrases,
                "phrases"=>$storyDat,
                "score"=>$score,
                "total"=>count($rtn),
            );
        }

        return $frtn;
    }

    private static function __toAssoc($d)
    {
        return json_decode(json_encode($d), true);
    }
}
