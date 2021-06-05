<?php

namespace App\Elastic\Rules;

use ScoutElastic\SearchRule;

class CategorySearchRule extends SearchRule
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
        $query = $this->builder->query;
        // if there's an exact match against the supplied query, return it as an exact requirement
        if (preg_match("/\"(.*)\"/", $query, $matches)) {
            return [
                'must' => [
                    'wildcard' => [
                        'exact_name' => [
                            'value' => trim("*{$matches[1]}*")
                        ]
                    ]
                ]
            ];
        }

        return [
            'should' => [
                [
                    'multi_match' => [
                        'query'     => $query,
                        'fuzziness' => 0
                    ]
                ]
            ]
        ];
    }
}
