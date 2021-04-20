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

        $query->getQuery()->columns ?
            $query->select($query->getQuery()->columns) :
            $query->select('*');

        $query
            ->addSelect(DB::raw('acos(least(greatest(sin('.$lat.')*sin(radians('.$latName.')) + cos('.$lat.')*cos(radians('.$latName.'))*cos(radians('.$lonName.')'.($lng < 0 ? '+'.abs($lng) : '-'.$lng).'), -1), 1)) * '.$r.' As distance'))
            ->fromSub(function ($query) use ($maxLat, $minLat, $maxLon, $minLon, $latName, $lonName) {
                $query->from($this->getTable())
                    ->whereBetween($latName, [$minLat, $maxLat])
                    ->whereBetween($lonName, [$minLon, $maxLon]);
            }, $this->getTable());
        if ($lng < 0) {
            $query->whereRaw(
                'acos(least(greatest(sin(?)*sin(radians('.$latName.')) + cos(?)*cos(radians('.$latName.'))*cos(radians('.$lonName.')+?), -1), 1)) * ? < ?',
                [$lat, $lat, abs($lng), $r, $radius]
            );
        } else {
            $query->whereRaw(
                'acos(least(greatest(sin(?)*sin(radians('.$latName.')) + cos(?)*cos(radians('.$latName.'))*cos(radians('.$lonName.')-?), -1), 1)) * ? < ?',
                [$lat, $lat, $lng, $r, $radius]
            );
        }
        $query->orderByRaw('distance');

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
