<?php
/**
 * Created by PhpStorm.
 * User: asafreedman
 * Date: 5/28/15
 * Time: 2:24 PM
 */

namespace Spinegar\Laravel\Facades;

use Illuminate\Support\Facades\Facade;


class Sugar7RestFacade extends Facade {
    protected static function getFacadeAccessor() { return 'sugar7'; }
}