<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // View::composer(['layouts.app'], function($view) 
        // {
        //     if(auth()->user())
        //     {
        //         $image=auth()->user()->images->first();
        //         $view->with(compact('image'));
        //     }
        // });
    }
}
