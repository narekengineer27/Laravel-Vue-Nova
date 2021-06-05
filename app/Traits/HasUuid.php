<?php

namespace App\Traits;

use App\Rules\Uuid;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

trait HasUuid {
    public static function bootHasUuid() {
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    /**
     * @param $query
     * @param $uuid
     * @return mixed
     */
    public function scopeUuid($query, $uuid)
    {
        if (!(new Uuid())->passes('uuid', $uuid)) {
            throw (new ModelNotFoundException)->setModel(get_class($this));
        }

        return $query->where('uuid', $uuid)->firstOrFail();
    }
}
