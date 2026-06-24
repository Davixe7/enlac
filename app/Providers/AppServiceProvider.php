<?php

namespace App\Providers;

use App\Models\Candidate;
use Illuminate\Support\ServiceProvider;
use App\Models\Program;
use App\Models\Sponsorship;
use App\Observers\CandidateObserver;
use App\Observers\ProgramObserver;
use App\Observers\SponsorshipObserver;
use Illuminate\Http\Resources\Json\JsonResource;

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
        Candidate::observe(CandidateObserver::class);
        Program::observe(ProgramObserver::class);
        Sponsorship::observe(SponsorshipObserver::class);
        //JsonResource::withoutWrapping();
    }
}
