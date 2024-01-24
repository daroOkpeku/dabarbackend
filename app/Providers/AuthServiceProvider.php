<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define("check-user", function($user){
             if($user->role == "user"){
              return true;
             }else{
                return false;
             }
        });

        Gate::define("check-editor", function($user){
            if($user->role == "editor"){
                return true;
               }else{
                  return false;
               }
        });


        // Gate::define("check-writer", function($user){
        //       if($user->role == "writer"){
        //           return true;
        //       }else{
        //         return false;
        //       }
        // });

        Gate::define("check-admin", function($user){
            if($user->role == "admin"){
                return true;
            }else{
              return false;
            }
        });

    }
}
