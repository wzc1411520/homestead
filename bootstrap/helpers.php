<?php
/**
 * Created by PhpStorm.
 * User: wzc
 * Date: 2018/10/17
 * Time: 23:42
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}