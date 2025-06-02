# SEOForge (Alpha)

[![Latest Version](https://poser.pugx.org/probeforge/seoforge/v/stable)](https://packagist.org/packages/probeforge/seoforge)
[![License](https://poser.pugx.org/probeforge/seoforge/license)](https://packagist.org/packages/probeforge/seoforge)

⚠️ **Alpha Version Warning**: This is an early development tool with limitations. It's not production-ready.

A basic SEO troubleshooting tool for Laravel 12 Blade templates. Scans your templates for missing SEO elements and can fix some basic structural issues.

## What This Tool Actually Does

**✅ What It Can Do:**
- Scan Blade templates for missing SEO elements (meta tags, Open Graph, etc.)
- Generate reports showing what's missing
- Add basic structural elements like viewport meta tag, canonical URLs, favicon links
- Create file backups before making changes
- Output results in table or JSON format

**❌ What It Cannot Do:**
- Write meta descriptions, titles, or any content for you
- Guarantee SEO success or rankings
- Fix semantic or content-related SEO issues
- Replace proper SEO strategy and planning
- Handle complex template logic or dynamic content perfectly

**🎯 Realistic Use Case:**
This is a development helper to quickly identify missing SEO elements in your templates. You still need to write the actual SEO content yourself.

## Installation

```bash
composer require probeforge/seoforge:@dev
```

## Quick Usage

### Check what's missing:
```bash
php artisan seo:audit
```

### Fix basic structural issues:
```bash
php artisan seo:fix --issues=viewport,canonical --backup
```

### Get JSON output for scripts:
```bash
php artisan seo:audit --json
```

## What Gets Checked

**Level A (Basic):**
- Title tags, meta descriptions, canonical URLs, viewport, lang attribute

**Level AA (Social):**
- Open Graph tags, Twitter cards, image alt attributes, heading hierarchy

**Level AAA (Advanced):**
- Schema markup, meta keywords, favicons, preconnect hints, hreflang

## What Can Be Auto-Fixed

Only basic structural elements:
- `viewport` - Adds viewport meta tag
- `canonical` - Adds canonical URL link
- `favicon` - Adds favicon link
- `language` - Adds lang attribute to html tag
- `meta_robots` - Adds robots meta tag
- `preconnect` - Adds preconnect/DNS prefetch
- `apple_touch_icon` - Adds Apple touch icon

**Important**: The tool cannot create content like meta descriptions or titles - that's your job!

## Commands

### `seo:audit`
```bash
php artisan seo:audit [--path=] [--level=A|AA|AAA] [--json]
```

### `seo:fix`
```bash
php artisan seo:fix [--path=] [--backup] [--issues=viewport,canonical]
```

## Example Blade Setup

```blade
<!DOCTYPE html>
<html lang="@yield('lang', 'en')">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>@yield('title', 'Default Title')</title>
    <meta name="description" content="@yield('description', 'Default description')">
    
    <link rel="canonical" href="@yield('canonical', request()->url())">
    <meta name="robots" content="@yield('robots', 'index,follow')">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', 'Default Title')">
    <meta property="og:description" content="@yield('og_description', 'Default description')">
    
    <link rel="icon" href="{{ asset('favicon.ico') }}">
</head>
<body>
    @yield('content')
</body>
</html>
```

Then in your pages:
```blade
@extends('layouts.app')

@section('title', 'About Us - My Site')
@section('description', 'Learn about our company and mission.')
@section('og_title', 'About Our Company')

@section('content')
    <h1>About Us</h1>
    <p>Content goes here...</p>
@endsection
```

## Known Issues & Limitations

- Table formatting can be messy in some terminals
- Regex detection may have false positives/negatives
- Cannot handle complex Blade logic
- Windows path handling has some edge cases
- Does not validate content quality, only presence
- Cannot generate meaningful content

## Configuration

Optional - publish config:
```bash
php artisan vendor:publish --provider="ProbeForge\SEOForge\SeoForgeServiceProvider"
```

## Is This For Me?

**Yes, if you want to:**
- Quickly scan templates for missing SEO elements
- Get a basic SEO checklist for your Laravel app
- Add basic structural SEO elements automatically

**No, if you expect:**
- AI-generated meta descriptions or titles
- Complete SEO optimization
- Production-ready SEO automation
- Marketing or content strategy advice

## Contributing

This is alpha software. Contributions welcome, but expect breaking changes.

## License

MIT License. See [LICENSE.md](LICENSE.md).

---

**Bottom Line**: This tool finds missing SEO elements and adds basic tags. You still need to write the actual SEO content yourself. It's a starting point, not a complete solution. 