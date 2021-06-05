<?php
namespace App\Elastic\Rules;

class ExploreBusinessesRule {
    /**
     * @param $lat
     * @param $lng
     * @param int $distance
     * @return array
     */
    public static function build($lat, $lng, $distance) {
        //prefer records that are within specified distance of supplied position (such as picked up by mobile GPS)
        return [
            'should' => [
                [
                    'geo_distance' => [
                        'distance' => "{$distance}km",
                        'location' => [
                            'lat' => $lat,
                            'lon' => $lng
                        ]
                    ]
                ]
            ]
        ];
    }
}
