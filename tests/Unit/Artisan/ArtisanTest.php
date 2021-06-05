<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/01/19
 * Time: 11:51 PM
 */

namespace Tests\Unit\Artisan;


use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ArtisanTest extends TestCase
{
    protected static $oldConn;
    use DatabaseMigrations;
    use RefreshDatabase;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$oldConn = getenv('DB_CONNECTION');
        putenv('DB_CONNECTION=testing');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $string = 'DB_CONNECTION='.self::$oldConn;
        putenv($string);
    }

    public function testRoutesAreSane()
    {
        Artisan::call('route:list');

        //Vacuous assertion to keep PHPUnit quiet
        $this->assertTrue(true);
    }

    public function testMigrationsRoundTripClean()
    {
        Artisan::call('db:seed');
        Artisan::call('migrate:rollback');

        //Vacuous assertion to keep PHPUnit quiet
        $this->assertTrue(true);
    }
}
