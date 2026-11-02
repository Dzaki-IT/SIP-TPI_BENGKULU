<?php

namespace App\Providers;

use App\Models\AlatTangkap;
use App\Models\JenisIkan;
use App\Models\Kapal;
use App\Models\Nelayan;
use App\Models\Pendaratan;
use App\Models\Tpi;
use App\Models\User;
use App\Policies\AlatTangkapPolicy;
use App\Policies\JenisIkanPolicy;
use App\Policies\KapalPolicy;
use App\Policies\NelayanPolicy;
use App\Policies\PendaratanPolicy;
use App\Policies\TpiPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
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
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::policy(AlatTangkap::class, AlatTangkapPolicy::class);
        Gate::policy(JenisIkan::class, JenisIkanPolicy::class);
        Gate::policy(Kapal::class, KapalPolicy::class);
        Gate::policy(Nelayan::class, NelayanPolicy::class);
        Gate::policy(Pendaratan::class, PendaratanPolicy::class);
        Gate::policy(Tpi::class, TpiPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
