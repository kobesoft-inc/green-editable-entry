<?php

namespace Green\EditableEntry;

use Illuminate\Support\ServiceProvider;

class EditableEntryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'green-editable-entry');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'green-editable-entry');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/green-editable-entry'),
        ], 'green-editable-entry-views');

        // Publish translations
        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/green-editable-entry'),
        ], 'green-editable-entry-lang');
    }
}
