<?php

/**
 * Model create helper
 */
function create($class, $attributes = [], $times = null)
{
    return factory($class, $times)->create($attributes, $times);
}

/**
 * Mode make helper
 */
function make($class, $attributes = [], $times = null)
{
    return factory($class, $times)->make($attributes);
}
