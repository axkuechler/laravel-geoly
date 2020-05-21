# Laravel Geoly

[![Packagist Version](https://img.shields.io/packagist/v/akuechler/laravel-geoly)](https://packagist.org/packages/akuechler/laravel-geoly)
![PHP from Packagist](https://img.shields.io/packagist/php-v/akuechler/laravel-geoly)
[![StyleCI](https://github.styleci.io/repos/226726169/shield?branch=master)](https://github.styleci.io/repos/226726169)
[![GitHub](https://img.shields.io/github/license/akuechler/laravel-geoly)](https://github.com/akuechler/laravel-geoly/blob/master/LICENSE)

Perform fast and efficient radius searches on your Laravel Eloquent models.

Laravel Geoly provides a convenient way for your Laravel Eloquent models to query in a certain radius around a position. It is lightning fast by using a bounding box to cut down the possible results and calculating the distance only on the remaining subset. Laravel Geoly works on both MySQL and PostgreSQL.

## Requirements

* PHP 7.1+
* Laravel 5+
* Tested on MySQL and PostgreSQL

## Installation

Simply require the project via composer:

`$ composer require akuechler/laravel-geoly`

## How to use

Geoly assumes the two columns `latitude` and `longitude` on your eloquent model. Simply add them to your migration if not present yet.

```php
$table->double('latitude');
$table->double('longitude');
``` 

If you prefer to use other names for your database columns, specify them in your model.

```php
const LATITUDE  = 'lat';
const LONGITUDE = 'lng';
```

Use the Geoly package within your Eloquent model.

```php
class YourModel extends Model
{
    use Geoly;
    ...
}
```

To search for all models within a specific radius around a position, add the `radius` scope to your query.

```php
$query = YourModel::radius($latitude, $longitude, $radius);
$query->get();
```

## Credits

This project is heavily inspired by [Laravel Geographical](https://github.com/malhal/Laravel-Geographical) and [Movable Type Scripts](https://www.movable-type.co.uk/) article on [Selecting points within a bounding circle](https://www.movable-type.co.uk/scripts/latlong-db.html).