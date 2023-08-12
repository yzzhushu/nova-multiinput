<?php

namespace Jshxl\MultiInput;

use Laravel\Nova\Nova;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('multi-input', __DIR__ . '/../dist/js/field.js');
//            Nova::style('multi-input', __DIR__ . '/../dist/css/field.css');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
