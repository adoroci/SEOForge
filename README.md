# SEOForge

[![Latest Stable Version](https://poser.pugx.org/probeforge/seoforge/v/stable)](https://packagist.org/packages/probeforge/seoforge)
[![Total Downloads](https://poser.pugx.org/probeforge/seoforge/downloads)](https://packagist.org/packages/probeforge/seoforge)
[![License](https://poser.pugx.org/probeforge/seoforge/license)](https://packagist.org/packages/probeforge/seoforge)

A comprehensive SEO audit and automated fixing tool for Laravel 12 applications. Analyze and optimize your Blade templates for search engine compliance with A/AA/AAA standards.

## ✨ Features

### 🔍 **Comprehensive SEO Auditing**
- **Multi-level compliance**: A, AA, AAA standards
- **Blade template analysis**: Deep inspection of Laravel templates
- **Dynamic content detection**: Identifies unfilled placeholders
- **Detailed reporting**: HTML reports with actionable insights

### 🛠️ **Automated Fixing**
- **One-command fixes**: Automatically resolve common SEO issues
- **Backup creation**: Safe modifications with rollback options
- **Selective fixing**: Target specific issues or run comprehensive fixes
- **Batch processing**: Fix multiple files simultaneously

### 📊 **Professional Reporting**
- **HTML reports**: Beautiful, shareable audit reports
- **JSON output**: Integrate with CI/CD pipelines
- **Progress tracking**: Monitor SEO improvements over time
- **Issue categorization**: Critical, moderate, and minor issues

### 🎯 **SEO Elements Covered**

#### **Level A (Critical)**
- ✅ Title tags
- ✅ Meta descriptions
- ✅ Canonical URLs
- ✅ Viewport meta tags
- ✅ HTML lang attributes

#### **Level AA (Important)**
- ✅ Meta robots directives
- ✅ Open Graph tags (title, description, image, type)
- ✅ Twitter Card tags
- ✅ Image alt attributes
- ✅ Heading hierarchy (H1-H6)

#### **Level AAA (Advanced)**
- ✅ Schema.org JSON-LD structured data
- ✅ Meta keywords
- ✅ DNS prefetch/preconnect hints
- ✅ Favicon and Apple touch icons
- ✅ Hreflang tags for internationalization
- ✅ Image dimensions attributes

## 📦 Installation

Install via Composer:

```bash
composer require probeforge/seoforge
```

The package will automatically register its service provider in Laravel 12.

### Publish Configuration (Optional)

```bash
php artisan vendor:publish --provider="ProbeForge\SEOForge\SeoForgeServiceProvider" --tag="config"
```

This publishes the configuration file to `config/seo-forge.php`.

## 🚀 Quick Start

### Basic SEO Audit

Run a comprehensive SEO audit on your Blade templates:

```bash
# Basic audit (AAA level)
php artisan seo:audit

# Specific compliance level
php artisan seo:audit --level=A

# Audit specific directory
php artisan seo:audit --path=resources/views/pages

# JSON output for CI/CD
php artisan seo:audit --level=AA --json
```

### Automated Fixing

Fix common SEO issues automatically:

```bash
# Fix all issues with backup
php artisan seo:fix --backup

# Fix specific issues
php artisan seo:fix --issues=viewport,canonical,meta_robots

# Fix specific directory
php artisan seo:fix --path=resources/views/pages --backup
```

### Generate Reports

Create comprehensive SEO reports:

```bash
# Generate HTML report
php artisan seo:report --output=public/seo-report.html

# Include dynamic content audit
php artisan seo:report --check-dynamic --output=reports/full-audit.html

# Specific compliance level
php artisan seo:report --level=AA --output=public/aa-compliance.html
```

## 📋 Available Commands

### `seo:audit`

Audit Blade templates for SEO compliance issues.

```bash
php artisan seo:audit [options]
```

**Options:**
- `--path=` : Path to scan (default: `resources/views`)
- `--level=` : Compliance level A/AA/AAA (default: `AAA`)
- `--json` : Output results as JSON
- `--fix` : Attempt to fix issues during audit

### `seo:fix`

Automatically fix common SEO issues in Blade templates.

```bash
php artisan seo:fix [options]
```

**Options:**
- `--path=` : Path to fix (default: `resources/views`)
- `--backup` : Create backups before modifying files
- `--issues=` : Specific issues to fix (comma-separated)

**Fixable Issues:**
- `viewport` : Add viewport meta tag
- `language` : Add HTML lang attribute
- `canonical` : Add canonical URLs
- `meta_robots` : Add robots meta tag
- `favicon` : Add favicon link
- `preconnect` : Add preconnect/DNS prefetch
- `apple_touch_icon` : Add Apple touch icon
- `missing_alt` : Add alt text to images

### `seo:audit-dynamic`

Audit dynamic content placeholders in templates.

```bash
php artisan seo:audit-dynamic [options]
```

**Options:**
- `--path=` : Path to scan (default: `resources/views`)
- `--config=` : Config file to check (default: `seo`)
- `--json` : Output as JSON

### `seo:report`

Generate comprehensive SEO audit reports.

```bash
php artisan seo:report [options]
```

**Options:**
- `--path=` : Path to scan (default: `resources/views`)
- `--level=` : Compliance level (default: `AAA`)
- `--output=` : Report output file (default: `report.html`)
- `--check-dynamic` : Include dynamic content audit

## ⚙️ Configuration

### SEO Configuration

Create a `config/seo.php` file to define your SEO settings:

```php
<?php

return [
    'pages' => [
        'home' => [
            'title' => 'Your App - Welcome',
            'description' => 'Welcome to our amazing application.',
            'keywords' => 'app, laravel, awesome',
        ],
        'about' => [
            'title' => 'About Us - Your App',
            'description' => 'Learn more about our company.',
            'keywords' => 'about, company, team',
        ],
    ],
    'default' => [
        'title' => 'Your App',
        'description' => 'Default description',
        'keywords' => 'default, keywords',
    ],
    'robots' => [
        'default' => 'index, follow',
        'admin' => 'noindex, nofollow',
    ],
];
```

### Package Configuration

The package configuration file (`config/seo-forge.php`) allows you to customize:

```php
<?php

return [
    'paths' => [
        'default_scan_path' => 'resources/views',
        'exclude_patterns' => [
            'resources/views/vendor/**',
            'resources/views/admin/**',
        ],
    ],
    'compliance' => [
        'default_level' => 'AAA',
        'strict_mode' => false,
    ],
    'reports' => [
        'default_output' => 'storage/app/seo-reports',
        'include_timestamps' => true,
    ],
    'fixes' => [
        'create_backups' => true,
        'backup_path' => 'storage/app/seo-backups',
    ],
];
```

## 🎨 Blade Template Integration

### Basic SEO Setup

Set up your main layout (`resources/views/layouts/app.blade.php`):

```blade
<!DOCTYPE html>
<html lang="@yield('lang', 'en')">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('seo.default.title'))</title>
    <meta name="description" content="@yield('meta_description', config('seo.default.description'))">
    <meta name="keywords" content="@yield('meta_keywords', config('seo.default.keywords'))">
    
    <!-- SEO Elements -->
    <link rel="canonical" href="@yield('canonical_url', request()->url())">
    <meta name="robots" content="@yield('meta_robots', config('seo.robots.default'))">
    
    <!-- Open Graph -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', request()->url())">
    <meta property="og:title" content="@yield('og_title', config('seo.default.title'))">
    <meta property="og:description" content="@yield('og_description', config('seo.default.description'))">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">
    
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('twitter_title', config('seo.default.title'))">
    <meta name="twitter:description" content="@yield('twitter_description', config('seo.default.description'))">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-default.png'))">
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    
    <!-- Resource Hints -->
    <link rel="preconnect" href="{{ config('app.url') }}">
    <link rel="dns-prefetch" href="{{ config('app.url') }}">
    
    <!-- Schema.org -->
    @yield('schema')
</head>
<body>
    @yield('content')
</body>
</html>
```

### Page-Specific SEO

In your page templates, define SEO elements:

```blade
@extends('layouts.app')

@section('title', 'About Us - ' . config('app.name'))
@section('meta_description', 'Learn about our company, mission, and team.')
@section('meta_keywords', 'about, company, team, mission')
@section('canonical_url', route('about'))

@section('og_title', 'About Our Company')
@section('og_description', 'Discover our story and meet our amazing team.')
@section('og_image', asset('images/about-og.png'))

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "AboutPage",
    "name": "About Us",
    "description": "Learn about our company, mission, and team."
}
</script>
@endsection

@section('content')
    <!-- Your page content -->
@endsection
```

## 📊 Understanding Audit Results

### Compliance Levels

- **Level A**: Critical SEO elements required for basic search engine indexing
- **Level AA**: Important elements that improve search visibility and social sharing
- **Level AAA**: Advanced optimizations for maximum SEO performance

### Issue Types

- **`missing_element`**: Required SEO element is not present
- **`invalid_format`**: Element exists but has incorrect format
- **`empty_content`**: Element exists but has no content
- **`dynamic_placeholder`**: Placeholder not filled with actual content

### Fixable Status

- **Fixable**: Can be automatically resolved with `seo:fix` command
- **Manual**: Requires manual intervention and content creation

## 🤝 Integration Examples

### CI/CD Pipeline

Add SEO auditing to your GitHub Actions:

```yaml
name: SEO Audit
on: [push, pull_request]

jobs:
  seo-audit:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.4
      - name: Install dependencies
        run: composer install
      - name: Run SEO Audit
        run: php artisan seo:audit --level=A --json > seo-audit.json
      - name: Upload audit results
        uses: actions/upload-artifact@v3
        with:
          name: seo-audit
          path: seo-audit.json
```

### Pre-commit Hook

Add to your `.git/hooks/pre-commit`:

```bash
#!/bin/sh
php artisan seo:audit --level=A --json | jq '.[] | select(.issues | length > 0)' && exit 1
echo "SEO audit passed"
```

## 🧪 Testing

Run the package tests:

```bash
composer test
```

Run with coverage:

```bash
composer test:coverage
```

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`
4. Check code style: `composer cs:check`

## 📝 Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

## 🔒 Security

If you discover any security-related issues, please email hi@probeforge.com instead of using the issue tracker.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 🙏 Credits

- [ProbeForge Team](https://github.com/probeforge)
- [All Contributors](../../contributors)

## 🔗 Links

- [Packagist](https://packagist.org/packages/probeforge/laravel-seo-audit)
- [GitHub](https://github.com/probeforge/laravel-seo-audit)
- [Documentation](https://docs.probeforge.com/laravel-seo-audit)
- [ProbeForge Tools](https://probeforge.com)

---

Made with ❤️ by Adnan Doroci [ProbeForge](https://probeforge.com) 