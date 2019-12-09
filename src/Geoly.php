<?php
/**
 *  Laravel-Geoly (https://github.com/akuechler/laravel-geoly).
 *
 *  Created by Alexsander Küchler on 12/8/2019.
 *  Copyright © 2019 Alexsander Küchler. All rights reserved.
 */

namespace Akuechler;

use Illuminate\Support\Facades\DB;

trait Geoly
{
    public function scopeRadius($query, $latitude, $longitude, $radius)
    {
        $r = 6371;  // earth's mean radius, km

        // first-cut bounding box (in degrees)
        $maxLat = $latitude + rad2deg($radius / $r);
        $minLat = $latitude - rad2deg($radius / $r);
        $maxLon = $longitude + rad2deg(asin($radius / $r) / cos(deg2rad($latitude)));
        $minLon = $longitude - rad2deg(asin($radius / $r) / cos(deg2rad($latitude)));

        $latName = $this->getLatitudeColumn();
        $lonName = $this->getLongitudeColumn();

        $lat = deg2rad($latitude);
        $lng = deg2rad($longitude);

        $query = $query
            ->selectRaw('acos(sin(?)*sin(radians('.$latName.')) + cos(?)*cos(radians('.$latName.'))*cos(radians('.$lonName.')-?)) * ? As distance', [$lat, $lat, $lng, $r])
            ->fromSub(function ($query) use ($maxLat, $minLat, $maxLon, $minLon, $latName, $lonName) {
                $query->from($this->getTable())
                    ->whereBetween($latName, [$minLat, $maxLat])
                    ->whereBetween($lonName, [$minLon, $maxLon]);
            }, 'bounding_box')
            ->whereRaw('acos(sin(?)*sin(radians('.$latName.')) + cos(?)*cos(radians('.$latName.'))*cos(radians('.$lonName.')-?)) * ? < ?',
                [$lat, $lat, $lng, $r, $radius])
            ->orderByRaw('distance');

        return $query;
    }

    public function getLatitudeColumn()
    {
        return defined('static::LATITUDE') ? static::LATITUDE : 'latitude';
    }

    public function getLongitudeColumn()
    {
        return defined('static::LONGITUDE') ? static::LONGITUDE : 'longitude';
    }
}
