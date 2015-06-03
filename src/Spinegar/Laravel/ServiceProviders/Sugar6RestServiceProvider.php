<?php
/**
 * Created by PhpStorm.
 * User: asafreedman
 * Date: 5/28/15
 * Time: 2:31 PM
 */

namespace Spinegar\Laravel\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class Sugar6RestServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('sugar6', '\Spinegar\Sugar7Wrapper\Rest');
    }

}