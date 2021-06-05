<?php

namespace App\Elastic\Rules;

use App\Models\Business;

class BusinessRule
{
    /**
     * @param $lat
     * @param $lng
     * @param string $query
     * @param null $categoryIds
     * @param $mapPreset
     * @return array
     */
    public static function build($lat, $lng, $query = '*', $categoryIds = null, $mapPreset = false)
    {
        $rule = [
            'function_score' => [
                'score_mode' => 'max',
                'query'      => [
                    'bool' => []
                ],
                'functions'  => []
            ]
        ];

        if ($mapPreset) {
            // if we have a mapPreset, dig out opening hours and make them mandatory
            $rule['function_score']['query']['bool']['must'][] = self::openedHours($mapPreset);
        } else {
            // otherwise prefer businesses that are open now
            $rule['function_score']['query']['bool']['should'][] = self::openedHours($mapPreset);
        }

        if ('*' !== $query) {
            // prefer matches to supplied query
            $rule['function_score']['query']['bool']['should'][] = self::queryName($query);
        } else {
            $rule['function_score']['query']['bool']['should'][] = self::queryMatchAll();
        }

        if (null !== $lat && null !== $lng) {
            // if a location's provided, scale results with gaussian decay using a 2 km scale length.
            // With three results, A, B, C, all have original scores of 100, but 0, 2 and 4 km away from origin,
            // their scores would be scaled to 100, 50 and 5(?)
            $rule['function_score']['functions'][] = [
                'gauss' => [
                    'location' => [
                        'origin' => "{$lat}, {$lng}",
                        'scale'  => '2km',
                    ],
                ]
            ];
        }

        // what happens if $categoryIds is already an array?
        if (null !== $categoryIds || $mapPreset) {
            $categoryIds = is_object($mapPreset) ? $mapPreset->categories->pluck('id')->toArray() : [$categoryIds];
            $rule['function_score']['query']['bool']['must'][] = [
                'nested' => [
                    'path'  => 'categories',
                    'query' => [
                        'bool' => [
                            'should' => [
                                'terms' => [
                                    'categories.pivot.category_id' => $categoryIds,
                                ],
                            ]
                        ]
                    ]
                ]
            ];
        }

        return $rule;
    }

    /**
     * @param string $query
     * @return array
     */
    private static function queryName(string $query): array
    {
        return [
            'multi_match' => [
                'query'     => $query,
                'fuzziness' => Business::fuzziness,
                'boost'     => Business::boostNameMatch
            ]
        ];
    }

    /**
     * @return array
     */
    private static function queryMatchAll()
    {
        return [
            'match_all' => new \stdClass()
        ];
    }

    /**
     * @param $mapPreset
     * @return array
     */
    public static function openedHours($mapPreset)
    {
        $query = [
            'nested' => [
                'boost' => Business::boostOpened,
                'path'  => 'hours',
                'query' => [
                    'bool' => [

                    ]
                ]
            ]
        ];

        if ($mapPreset && !empty($mapPreset->businessHours->toArray())) {
            foreach ($mapPreset->businessHours as $businessHour) {
                $hours = [];
                // for the days matched by this businessHour model...
                for ($i = 0; $i < 7; $i++) {
                    if ($businessHour->{"wd_{$i}"}) {
                        $hours[] = $i;
                    }
                }
                // ... only match those records whose opening hours are a non-strict superset of the supplied opening
                // hours - if we supply 11 am to 3:50pm inclusive, then a record opening at 11 am and closing at 4 pm
                // would count.
                $query['nested']['query']['bool']['must'][] = [
                    [
                        'terms' => [
                            'hours.day_of_week' => $hours
                        ]
                    ],
                    [
                        'range' => [
                            'hours.open_period_mins' => [
                                'lte' => $businessHour->open_period_mins
                            ]
                        ]
                    ],
                    [
                        'range' => [
                            'hours.close_period_mins' => [
                                'gte' => $businessHour->close_period_mins
                            ]
                        ]
                    ]
                ];
            }
        } else {
            // If we don't have a mapPreset or valid businessHours, match businesses that are currently
            // open today.  Can this be remixed to use Carbon to make it more easily testable?
            // Further, can the null case be recast as a special case of the has-match case to simplify things?
            $query['nested']['query']['bool']['must'] = [
                [
                    'match' => [
                        'hours.day_of_week' => date('w'),
                    ]
                ],
                [
                    'range' => [
                        'hours.open_period_mins' => [
                            'lte' => Business::currentMinutes()
                        ]
                    ]
                ],
                [
                    'range' => [
                        'hours.close_period_mins' => [
                            'gte' => Business::currentMinutes()
                        ]
                    ]
                ]
            ];
        }

        return [$query];
    }
}
