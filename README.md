# Laravel Geoly

Perform fast and efficient radius searches on your Laravel Eloquent models.

Laravel Geoly provides a convenient scope for your Laravel Eloquent models to query in a certain radius. It is lightning fast by using a bounding box to cut down the possible results and calculating the distance only on this subset. Laravel Geoly works on both MySQL and PostgreSQL.

## Requirements

* PHP 5.6.4+
* Laravel 5+ or 6+
* Tested on MySQL and PostgreSQL

## Installation

Simply require the project via composer:

`$ composer require akuechler/laravel-geoly`

## How to use

Geoly assumes the two columns `latitude` and `longitude` on your eloquent model. Simply add them to your migration.

```php
$table->double('latitude');
$table->double('longitude');
``` 

If you prefer to use other names for your database columns, specify them in your Model.

```
const LATITUDE  = 'lat';
const LONGITUDE = 'lng';
```

Use the Geoly package within your Eloquent model.

```
class YourModel extends Model
{
    use Geoly;
    ...
}
```

To search for all models within a specific radius around a position, add the `radius` scope to your query.

```
$query = YourModel::radius($latitude, $longitude, $radius);
$query->get();
```

## Credits

This project is heavily inspired by [Laravel Geographical](https://github.com/malhal/Laravel-Geographical) and [Movable Type Scripts](https://www.movable-type.co.uk/) article on [Selecting points within a bounding circle](https://www.movable-type.co.uk/scripts/latlong-db.html).