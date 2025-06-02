# Changelog

All notable changes to `seoforge` will be documented in this file.

## [1.0] - 2025-06-02

### Added
- Initial release of SEOForge package
- Comprehensive SEO auditing for Laravel 12 Blade templates
- Support for A/AA/AAA compliance levels
- Automated fixing of common SEO issues
- HTML report generation
- JSON output for CI/CD integration
- Backup functionality for safe modifications
- SEO configuration management
- Command-line tools:
  - `seo:audit` - Audit templates for SEO compliance
  - `seo:fix` - Automatically fix common SEO issues
  - `seo:audit-dynamic` - Audit dynamic content placeholders
  - `seo:report` - Generate comprehensive SEO reports

### Features
- **Multi-level Compliance**: A, AA, AAA standards support
- **Blade Template Integration**: Native Laravel template support
- **Automated Fixes**: One-command resolution of common issues
- **Professional Reporting**: HTML and JSON output formats
- **Safe Operations**: Backup creation and rollback capabilities
- **Extensive Coverage**: 24+ SEO elements checked
- **CI/CD Ready**: JSON output for pipeline integration
- **Customizable**: Configurable compliance levels and patterns

### SEO Elements Covered
#### Level A (Critical)
- Title tags
- Meta descriptions
- Canonical URLs
- Viewport meta tags
- HTML lang attributes

#### Level AA (Important)
- Meta robots directives
- Open Graph tags (title, description, image, type)
- Twitter Card tags
- Image alt attributes
- Heading hierarchy (H1-H6)

#### Level AAA (Advanced)
- Schema.org JSON-LD structured data
- Meta keywords
- DNS prefetch/preconnect hints
- Favicon and Apple touch icons
- Hreflang tags for internationalization
- Image dimensions attributes 