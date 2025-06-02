<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Scan Paths
    |--------------------------------------------------------------------------
    |
    | These are the default paths that will be scanned for SEO issues when
    | no specific path is provided to the commands.
    |
    */
    'paths' => [
        'default_scan_path' => 'resources/views',
        'exclude_patterns' => [
            'resources/views/vendor/**',
            'resources/views/mail/**',
            'resources/views/errors/**',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Compliance Settings
    |--------------------------------------------------------------------------
    |
    | Configure the default compliance level and behavior of the SEO audit.
    |
    */
    'compliance' => [
        'default_level' => 'AAA',
        'strict_mode' => false,
        'ignore_components' => true, // Skip component files by default
    ],

    /*
    |--------------------------------------------------------------------------
    | Report Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for generating SEO audit reports.
    |
    */
    'reports' => [
        'default_output' => 'storage/app/seo-reports',
        'include_timestamps' => true,
        'template' => 'default', // Report template to use
    ],

    /*
    |--------------------------------------------------------------------------
    | Fix Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the automated SEO fixing functionality.
    |
    */
    'fixes' => [
        'create_backups' => true,
        'backup_path' => 'storage/app/seo-backups',
        'safe_mode' => true, // Only apply safe fixes by default
    ],

    /*
    |--------------------------------------------------------------------------
    | Elements Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which SEO elements should be checked at each compliance level.
    |
    */
    'elements' => [
        'A' => [
            'title' => 'Title tag',
            'meta_description' => 'Meta description',
            'canonical' => 'Canonical URL',
            'viewport' => 'Viewport meta tag',
            'language' => 'HTML lang attribute',
        ],
        'AA' => [
            'meta_robots' => 'Robots meta tag',
            'og_title' => 'Open Graph title',
            'og_description' => 'Open Graph description',
            'og_image' => 'Open Graph image',
            'twitter_card' => 'Twitter card',
            'image_alt' => 'Image alt attributes',
            'headings_hierarchy' => 'Heading hierarchy',
        ],
        'AAA' => [
            'schema_json' => 'Schema.org JSON-LD',
            'meta_keywords' => 'Meta keywords',
            'preconnect' => 'Preconnect/DNS prefetch',
            'image_dimensions' => 'Image width and height attributes',
            'dynamic_placeholders' => 'Dynamic placeholders filled',
            'favicon' => 'Favicon',
            'apple_touch_icon' => 'Apple touch icon',
            'language_alternates' => 'Hreflang tags',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fixable Issues
    |--------------------------------------------------------------------------
    |
    | Define which issues can be automatically fixed by the seo:fix command.
    |
    */
    'fixable_issues' => [
        'viewport',
        'language',
        'meta_robots',
        'canonical',
        'favicon',
        'preconnect',
        'apple_touch_icon',
        'missing_alt',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Patterns
    |--------------------------------------------------------------------------
    |
    | Add custom regex patterns for detecting SEO elements in your templates.
    |
    */
    'custom_patterns' => [
        // Add custom patterns here
        // 'custom_element' => '/your-regex-pattern/',
    ],
]; 