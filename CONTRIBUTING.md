# Contributing to SEOForge

Thank you for considering contributing to SEOForge! We welcome contributions from the community.

⚠️ **Note**: This is an alpha project under active development. Expect breaking changes and bugs.

## Development Setup

1. Fork the repository on GitHub
2. Clone your fork locally:
   ```bash
   git clone https://github.com/YOUR-USERNAME/SEOForge.git
   cd SEOForge
   ```

3. Install dependencies:
   ```bash
   composer install
   ```

4. Create a branch for your feature or fix:
   ```bash
   git checkout -b feature/your-feature-name
   ```

## Running Tests

Run the test suite to ensure your changes don't break existing functionality:

```bash
composer test
```

Run tests with coverage:
```bash
composer test:coverage
```

## Code Style

We follow PSR-12 coding standards. Run the code style fixer:

```bash
composer cs:fix
```

Check code style:
```bash
composer cs:check
```

## Static Analysis

Run PHPStan for static analysis:
```bash
composer analyze
```

## Submitting Changes

1. **Create an Issue First**: For new features, please create an issue to discuss the change before implementing it.

2. **Write Tests**: All new features should include tests. Bug fixes should include regression tests.

3. **Update Documentation**: Update the README.md if you're adding new features or changing existing functionality.

4. **Follow Commit Conventions**: Use clear, descriptive commit messages.

5. **Submit a Pull Request**: 
   - Include a clear description of what your change does
   - Reference any related issues
   - Ensure all tests pass
   - Make sure your code follows the project's coding standards

## Pull Request Guidelines

- **Target the `main` branch** for all pull requests
- **One feature per PR** - keep changes focused and atomic
- **Include tests** for any new functionality
- **Update documentation** as needed
- **Follow semantic versioning** for any breaking changes

## Adding New SEO Checks

When adding new SEO elements to check:

1. Add the element to the appropriate compliance level in `config/seo-forge.php`
2. Implement the detection logic in the `SeoAudit` command
3. If the element can be auto-fixed, add it to the `SeoFixer` command
4. Add tests for your new functionality
5. Update the README.md documentation

## Alpha Development Priorities

Since this is alpha software, we're focusing on:

1. **Core functionality** - Making audit and fix commands reliable
2. **Cross-platform compatibility** - Windows/Unix path handling
3. **Accurate detection** - Improving regex patterns
4. **Better error handling** - More graceful failures
5. **Documentation** - Clear, honest documentation

## Reporting Issues

When reporting issues, please include:

- Laravel version
- PHP version  
- Package version
- Operating system
- Steps to reproduce the issue
- Expected vs actual behavior
- Any relevant code samples

## Feature Requests

Feature requests are welcome! Please:

- Check existing issues to avoid duplicates
- Clearly describe the feature and its use case
- Explain why it would be valuable to other users
- Consider contributing the implementation yourself
- Keep in mind this is alpha software with limited scope

## Alpha Limitations

Remember that SEOForge is currently:

- ❌ Not production-ready
- ❌ Limited to basic structural SEO elements
- ❌ Cannot generate content (meta descriptions, titles, etc.)
- ✅ Good for identifying missing SEO elements
- ✅ Useful for adding basic tags

## Code of Conduct

Please note that this project is released with a [Code of Conduct](CODE_OF_CONDUCT.md). By participating in this project you agree to abide by its terms.

## Recognition

Contributors will be recognized in the README.md file and release notes.

Thank you for helping make SEOForge better! 