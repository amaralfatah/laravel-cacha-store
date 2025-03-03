<?php

if (!function_exists('setActive')) {
    function setActive($route)
    {
        return request()->routeIs($route) ? 'active' : '';
    }
}

if (!function_exists('setOpen')) {
    function setOpen($route)
    {
        return request()->routeIs($route) ? 'open' : '';
    }
}

