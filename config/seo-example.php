<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Page SEO Configuration
    |--------------------------------------------------------------------------
    |
    | Define SEO settings for your application's pages. These will be used
    | in your Blade templates with @yield() directives.
    |
    */
    'pages' => [
        'home' => [
            'title' => env('APP_NAME', 'Laravel') . ' - Welcome',
            'description' => 'Welcome to our amazing Laravel application. Discover our features and get started today.',
            'keywords' => 'laravel, web application, php, framework',
        ],
        'about' => [
            'title' => 'About Us - ' . env('APP_NAME', 'Laravel'),
            'description' => 'Learn more about our company, our mission, and the team behind our innovative solutions.',
            'keywords' => 'about us, company, team, mission, values',
        ],
        'contact' => [
            'title' => 'Contact Us - ' . env('APP_NAME', 'Laravel'),
            'description' => 'Get in touch with us. We would love to hear from you and answer any questions you may have.',
            'keywords' => 'contact, support, help, get in touch, customer service',
        ],
        'services' => [
            'title' => 'Our Services - ' . env('APP_NAME', 'Laravel'),
            'description' => 'Explore our comprehensive range of services designed to meet your business needs.',
            'keywords' => 'services, solutions, business, professional services',
        ],
        'faq' => [
            'title' => 'Frequently Asked Questions - ' . env('APP_NAME', 'Laravel'),
            'description' => 'Find answers to common questions about our services, features, and how to get started.',
            'keywords' => 'faq, questions, answers, help, support, documentation',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tools/Features SEO Configuration
    |--------------------------------------------------------------------------
    |
    | If your application has tools or features, define their SEO settings here.
    |
    */
    'tools' => [
        'dashboard' => [
            'title' => 'Dashboard - ' . env('APP_NAME', 'Laravel'),
            'description' => 'Your personal dashboard with all the tools and information you need.',
            'keywords' => 'dashboard, tools, user interface, management',
        ],
        'analytics' => [
            'title' => 'Analytics - ' . env('APP_NAME', 'Laravel'),
            'description' => 'Comprehensive analytics and reporting for data-driven decisions.',
            'keywords' => 'analytics, reports, data, insights, statistics',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default SEO Settings
    |--------------------------------------------------------------------------
    |
    | Default values that will be used when specific page settings are not found.
    |
    */
    'default' => [
        'title' => env('APP_NAME', 'Laravel'),
        'description' => 'A modern Laravel application built with best practices and powerful features.',
        'keywords' => 'laravel, php, web application, framework, modern',
        'author' => 'Your Company Name',
        'site_name' => env('APP_NAME', 'Laravel'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Robots Meta Tag Settings
    |--------------------------------------------------------------------------
    |
    | Configure robots directives for different sections of your site.
    |
    */
    'robots' => [
        'default' => 'index, follow',
        'admin' => 'noindex, nofollow',
        'auth' => 'noindex, nofollow',
        'api' => 'noindex, nofollow',
        'staging' => 'noindex, nofollow',
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for Open Graph and Twitter Cards.
    |
    */
    'social' => [
        'og' => [
            'site_name' => env('APP_NAME', 'Laravel'),
            'locale' => 'en_US',
            'type' => 'website',
            'default_image' => 'images/og-default.png',
        ],
        'twitter' => [
            'site' => '@YourTwitterHandle',
            'creator' => '@YourTwitterHandle',
            'card' => 'summary_large_image',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Schema.org Configuration
    |--------------------------------------------------------------------------
    |
    | Default structured data for your site.
    |
    */
    'schema' => [
        'organization' => [
            '@type' => 'Organization',
            'name' => env('APP_NAME', 'Laravel'),
            'url' => env('APP_URL', 'http://localhost'),
            'logo' => env('APP_URL', 'http://localhost') . '/images/logo.png',
            'sameAs' => [
                'https://twitter.com/YourTwitterHandle',
                'https://www.facebook.com/YourFacebookPage',
                'https://www.linkedin.com/company/YourCompany',
            ],
        ],
        'website' => [
            '@type' => 'WebSite',
            'name' => env('APP_NAME', 'Laravel'),
            'url' => env('APP_URL', 'http://localhost'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => env('APP_URL', 'http://localhost') . '/search?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Internationalization
    |--------------------------------------------------------------------------
    |
    | Hreflang settings for multi-language sites.
    |
    */
    'i18n' => [
        'default_locale' => 'en',
        'supported_locales' => ['en', 'es', 'fr', 'de'],
        'alternate_urls' => [
            'en' => env('APP_URL', 'http://localhost'),
            'es' => env('APP_URL', 'http://localhost') . '/es',
            'fr' => env('APP_URL', 'http://localhost') . '/fr',
            'de' => env('APP_URL', 'http://localhost') . '/de',
        ],
    ],
]; 