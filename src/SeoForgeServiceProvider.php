<?php

namespace ProbeForge\SEOForge;

use Illuminate\Support\ServiceProvider;
use ProbeForge\SEOForge\Console\Commands\SeoAudit;
use ProbeForge\SEOForge\Console\Commands\SeoFixer;
use ProbeForge\SEOForge\Console\Commands\SeoAuditDynamicContent;
use ProbeForge\SEOForge\Console\Commands\SeoReport;

class SeoForgeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/seo-forge.php',
            'seo-forge'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/../config/seo-forge.php' => config_path('seo-forge.php'),
        ], 'config');

        // Publish example SEO configuration
        $this->publishes([
            __DIR__.'/../config/seo-example.php' => config_path('seo.php'),
        ], 'seo-config');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SeoAudit::class,
                SeoFixer::class,
                SeoAuditDynamicContent::class,
                SeoReport::class,
            ]);
        }
    }
} 