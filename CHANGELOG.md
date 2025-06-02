# Changelog

All notable changes to `seoforge` will be documented in this file.

## [0.1.0-alpha] - 2025-06-02

### Alpha Release Notes
⚠️ **This is an early alpha version** - expect bugs and limitations!

### What Works
- **SEO Detection**: Scans Blade templates for missing SEO elements (title, meta tags, Open Graph, etc.)
- **Basic Auto-fixes**: Can add structural elements like viewport meta tag, canonical URLs, favicon links
- **Audit Reports**: Table and JSON output showing what's missing
- **Backup System**: Creates backups before making changes
- **Compliance Levels**: A/AA/AAA standards checking

### What Doesn't Work Yet / Limitations
- **No Content Creation**: Cannot generate meta descriptions, titles, or any actual content
- **Limited Fixes**: Only fixes basic structural elements, not semantic issues
- **Basic Detection**: Regex-based detection may have false positives/negatives
- **Windows Path Issues**: Some edge cases with file path handling
- **Table Display**: Minor formatting issues in terminal output

### What This Tool Does (Realistically)
- ✅ Finds missing SEO elements in your templates
- ✅ Helps troubleshoot SEO setup issues
- ✅ Can add basic structural tags (viewport, canonical, etc.)
- ❌ Does NOT write your meta descriptions for you
- ❌ Does NOT guarantee 100% SEO compliance
- ❌ Does NOT replace proper SEO planning and content strategy

### Fixed in This Version
- **Cross-platform file discovery**: Better Windows compatibility
- **Backup functionality**: Proper timestamped backup creation
- **Progress reporting**: Accurate file count display
- **Command reliability**: Both audit and fix operations work without hanging

### Known Issues
- Table formatting can be messy in some terminals
- Some regex patterns may need refinement
- Limited error handling for edge cases

### Tested On
- ✅ Windows 10 PowerShell
- ✅ Laravel 12 with PHP 8.4
- ✅ Basic Blade templates

This is a development tool to help identify SEO issues - you still need to write the actual content! 