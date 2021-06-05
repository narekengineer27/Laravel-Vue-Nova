<?php

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    public static function generatePIN($digits = 5)
    {
        return rand(pow(10, $digits-1), pow(10, $digits)-1);
    }

    public static function inBounds($coordinates, $west, $south, $east, $north)
    {
        return ($coordinates[0] - $east) * ($coordinates[0] - $west) < 0 &&
                ($coordinates[1] - $north) * ($coordinates[1] - $south) < 0;
    }
}
