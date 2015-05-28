<?php
/**
 * Created by PhpStorm.
 * User: asafreedman
 * Date: 5/28/15
 * Time: 2:31 PM
 */

namespace Spinegar\Laravel\ServiceProviders;


class SugarRestServiceProvider {
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('sugar', 'Spinegar\Sugar7Wrapper\Rest');
    }

}