<?php

namespace App\Providers;

use App\Models\Field;
use App\Models\Level;
use Illuminate\Support\ServiceProvider;
use App\Services\Base\Providers\BaseServiceRegisterProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //服務註冊
        $this->app->register(BaseServiceRegisterProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //$fields = Field::all()->except(1);
        $fields = Field::where('isAll',0)->get();
        $levels = Level::all();
        view()->share('fields', $fields);
        view()->share('levels', $levels);
    }
}
