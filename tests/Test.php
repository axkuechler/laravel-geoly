<?php

namespace Akuechler\Test;

use Akuechler\Test\Models\House;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class Test extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }

    /** @test */
    public function it_runs_the_migrations()
    {
        $this->assertEquals([
            'id',
            'name',
            'latitude',
            'longitude',
            'created_at',
            'updated_at',
        ], DB::getSchemaBuilder()->getColumnListing('houses'));
    }

    /** @test */
    public function it_creates_a_house()
    {
        House::create([
            'name'      => 'Test House',
            'latitude'  => 8.6,
            'longitude' => 8.6,
        ]);

        $this->assertDatabaseHas('houses', [
            'name'      => 'Test House',
            'latitude'  => 8.6,
            'longitude' => 8.6,
        ]);
    }

    /** @test */
    public function it_calculates_distance_correctly_with_lat_positive_and_lng_postitive()
    {
        House::create([
            'name'      => 'Test House',
            'latitude'  => 8.6,
            'longitude' => 8.6,
        ]);

        $houses = House::radius(8.6, 8.6, 2)->get();
        $this->assertCount(1, $houses);
    }

    /** @test */
    public function it_calculates_distance_correctly_with_lat_positive_and_lng_negative()
    {
        House::create([
            'name'      => 'Test House',
            'latitude'  => 8.6,
            'longitude' => -8.6,
        ]);

        $houses = House::radius(8.6, -8.6, 2)->get();
        $this->assertCount(1, $houses);
    }

    /** @test */
    public function it_calculates_distance_correctly_with_lat_negative_and_lng_postitive()
    {
        House::create([
            'name'      => 'Test House',
            'latitude'  => -8.6,
            'longitude' => 8.6,
        ]);

        $houses = House::radius(-8.6, 8.6, 2)->get();
        $this->assertCount(1, $houses);
    }

    /** @test */
    public function it_calculates_distance_correctly_with_lat_negative_and_lng_negative()
    {
        House::create([
            'name'      => 'Test House',
            'latitude'  => -8.6,
            'longitude' => -8.6,
        ]);

        $houses = House::radius(-8.6, -8.6, 2)->get();
        $this->assertCount(1, $houses);
    }

    /** @test */
    public function it_handles_rounding_errors_correctly_in_acos_calculations()
    {
        House::create([
            'name'      => 'Test House',
            'latitude'  => 59.5598793,
            'longitude' => 30.1385594,
        ]);

        $houses = House::radius(59.5598793, 30.1385594, 2)->get();
        $this->assertCount(1, $houses);
    }
}
