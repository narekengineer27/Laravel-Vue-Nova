<?php

namespace App\Services\Api;

use App\Models\Business;
use App\Models\MapPreset;

class MapPresetService
{
    /**
     * @var MapPreset
     */
    private $model;

    /**
     * MapPresetService constructor.
     * @param MapPreset $mapPreset
     */
    public function __construct(MapPreset $mapPreset)
    {
        $this->model = $mapPreset;
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->model->get();
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search(array $data)
    {
        $mapPreset = $this->model->uuid($data['map_preset_uuid']);
        $businessIds = [];
        $lat = (float) $data['lat'];
        $lon = (float) $data['lon'];
        foreach ($mapPreset->categories as $category) {
            $businesses = $category->businesses();
            if ($mapPreset->showOnlyOpen()) {
                $businesses = $businesses->where('is_open', true);
            }
            $businesses = $businesses->get()->pluck('id')->toArray();
            $businessIds = array_merge($businessIds, $businesses);
        }
        return Business::search('*')->whereIn('id', $businessIds)->whereGeoDistance('location', [$lon, $lat], "10km")->paginate();
    }
}
