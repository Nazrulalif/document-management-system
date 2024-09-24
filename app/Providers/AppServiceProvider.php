<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\documentVersion;
use App\Models\Folder;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Observers\ModelDocument;
use App\Observers\ModelDocumentVersion;
use App\Observers\ModelFolder;
use App\Observers\ModelOrganization;
use App\Observers\ModelRole;
use App\Observers\ModelUser;
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
        Organization::observe(ModelOrganization::class);
        User::observe(ModelUser::class);
        Role::observe(ModelRole::class);
        Folder::observe(ModelFolder::class);
        Document::observe(ModelDocument::class);
    }
}
