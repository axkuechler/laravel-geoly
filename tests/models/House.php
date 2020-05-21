<?php

namespace Akuechler\Test\Models;

use Akuechler\Geoly;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use Geoly;

    protected $fillable = ['name', 'latitude', 'longitude'];
}
