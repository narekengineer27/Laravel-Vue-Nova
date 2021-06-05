<?php

namespace App\Elastic\Rules;

use ScoutElastic\SearchRule;

class BusinessSearchRule extends SearchRule
{
    /**
     * @inheritdoc
     */
    public function buildHighlightPayload()
    {
        return [
            'fields' => [
                'name' => [
                    'type' => 'plain'
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function buildQueryPayload()
    {
        $keyword = $this->builder->query;

        if (strlen($keyword) >= 4) {
            $fuzz = strlen($keyword) >= 6 ? 2 : 1;
        } else {
            $fuzz = 0;
        }

        $queryMatch = [
            'must' => [
                'fuzzy' =>
                    [
                        'name' =>
                            [
                                'value' => $keyword,
                                'boost' => 1,
                                'fuzziness' => $fuzz,
                                'prefix_length' => 0,
                                'max_expansions' => 100,
                            ],
                    ],
            ],
        ];

        return $queryMatch;
    }
}
