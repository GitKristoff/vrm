<?php

namespace App\Providers;

use App\Models\Pet;
use App\Models\Conversation;
use App\Policies\PetPolicy;
use App\Policies\ConversationPolicy;
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
        // Register Pet Policy
        Gate::policy(Pet::class, PetPolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);
    }
}
