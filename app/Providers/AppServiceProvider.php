<?php

namespace App\Providers;
use Illuminate\Http\Request;
use App\Models\ipadress;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Request $request): void
    {

        // $clientIp = $request->header('x-real-ip') ?: $request->ip();
        // $ip = ipadress::where('ip', $clientIp)->first();
        // if(!$ip){
        //  ipadress::create([
        //         'ip'=>$clientIp,
        //          'is_status'=>0,
        //  ]);
        // }


    }
}
