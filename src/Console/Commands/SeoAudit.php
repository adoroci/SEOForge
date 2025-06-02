<?php

namespace ProbeForge\SEOForge\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class SeoAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:audit 
                            {--path=resources/views : Path to scan for blade files}
                            {--level=AAA : Audit level (A, AA, AAA)}
                            {--json : Output as JSON}
                            {--fix : Attempt to fix common issues}
                            {--interactive : Interactive mode with fix suggestions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '🔍 Scan Blade templates for missing SEO elements (alpha tool)';

    /**
     * Required SEO elements by compliance level
     */
    protected array $requiredElements = [
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
    ];

    /**
     * Audit results
     */
    protected array $results = [];
    
    /**
     * Path being audited
     */
    protected string $scanPath;
    
    /**
     * Compliance level
     */
    protected string $complianceLevel;
    
    /**
     * Should fix issues
     */
    protected bool $shouldFix;

    /**
     * Interactive mode
     */
    protected bool $isInteractive;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->scanPath = $this->option('path');
        $this->complianceLevel = strtoupper($this->option('level'));
        $this->shouldFix = $this->option('fix');
        $this->isInteractive = $this->option('interactive');
        
        // Show welcome banner
        $this->showWelcomeBanner();
        
        if (!in_array($this->complianceLevel, ['A', 'AA', 'AAA'])) {
            $this->error("❌ Invalid compliance level. Must be one of: A, AA, AAA");
            return 1;
        }

        if (!File::exists($this->scanPath)) {
            $this->error("❌ Path not found: {$this->scanPath}");
            return 1;
        }

        $this->info("🔍 Starting SEO audit at <fg=yellow>{$this->complianceLevel}</> compliance level...");
        $this->line("<fg=gray>📂 Scanning path: {$this->scanPath}</>");
        
        $this->scanBladeFiles();
        
        // Output results
        if ($this->option('json')) {
            $this->outputJson();
        } else {
            $this->outputEnhancedTable();
            
            // Show interactive suggestions if requested
            if ($this->isInteractive && $this->hasFixableIssues()) {
                $this->showInteractiveSuggestions();
            }
        }
        
        return 0;
    }

    /**
     * Show welcome banner
     */
    protected function showWelcomeBanner(): void
    {
        $this->line('');
        $this->line('<fg=cyan>╔══════════════════════════════════════════════╗</>');
        $this->line('<fg=cyan>║</> <fg=white;options=bold>🔍 SEOForge - Laravel SEO Auditor (Alpha)</> <fg=cyan>║</>');
        $this->line('<fg=cyan>║</> <fg=gray>Finds missing SEO elements - content up to you</> <fg=cyan>║</>');
        $this->line('<fg=cyan>╚══════════════════════════════════════════════╝</>');
        $this->line('');
    }
    
    /**
     * Scan blade files for SEO issues
     */
    protected function scanBladeFiles(): void
    {
        $files = $this->getBladeFiles();
        $this->info("📄 Found <fg=yellow>" . count($files) . "</> blade files to audit");
        
        $bar = $this->output->createProgressBar(count($files));
        $bar->setFormat('🔄 Processing: %current%/%max% [%bar%] %percent:3s%% %message%');
        $bar->start();
        
        foreach ($files as $file) {
            $fileName = basename($file);
            $bar->setMessage("Auditing: <fg=cyan>{$fileName}</>");
            $this->auditFile($file);
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("\n");
    }
    
    /**
     * Get all blade files in the specified path
     */
    protected function getBladeFiles(): array
    {
        $files = [];
        
        // Check if the path is a specific file
        if (is_file($this->scanPath) && str_ends_with($this->scanPath, '.blade.php')) {
            return [$this->scanPath];
        }
        
        // If it's a directory, scan recursively
        if (is_dir($this->scanPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->scanPath, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }
    
    /**
     * Audit a single file
     */
    protected function auditFile(string $file): void
    {
        $relativePath = str_replace(base_path() . '/', '', $file);
        $content = File::get($file);
        
        $fileResults = [
            'file' => $relativePath,
            'issues' => [],
            'compliance' => 'AAA',
        ];
        
        // Check for required elements
        $requiredForLevel = $this->getRequiredElementsForLevel();
        foreach ($requiredForLevel as $elementKey => $elementName) {
            if (!$this->checkElement($content, $elementKey)) {
                $issue = [
                    'type' => 'missing_element',
                    'element' => $elementName,
                    'severity' => $this->getElementSeverity($elementKey),
                    'fixable' => $this->isFixable($elementKey),
                ];
                
                $fileResults['issues'][] = $issue;
                
                // Downgrade compliance level
                if ($issue['severity'] === 'A') {
                    $fileResults['compliance'] = 'Non-compliant';
                } elseif ($issue['severity'] === 'AA' && $fileResults['compliance'] === 'AAA') {
                    $fileResults['compliance'] = 'AA';
                } elseif ($issue['severity'] === 'AAA' && $fileResults['compliance'] === 'AAA') {
                    $fileResults['compliance'] = 'AA';
                }
            }
        }
        
        // Store results
        $this->results[$relativePath] = $fileResults;
    }
    
    /**
     * Get required elements for the current compliance level
     */
    protected function getRequiredElementsForLevel(): array
    {
        $required = [];
        
        // Add elements from current level and all lower levels
        if ($this->complianceLevel === 'AAA') {
            $required = array_merge($required, $this->requiredElements['AAA']);
        }
        if ($this->complianceLevel === 'AAA' || $this->complianceLevel === 'AA') {
            $required = array_merge($required, $this->requiredElements['AA']);
        }
        $required = array_merge($required, $this->requiredElements['A']);
        
        return $required;
    }
    
    /**
     * Check if an element exists in the content
     */
    protected function checkElement(string $content, string $elementKey): bool
    {
        switch ($elementKey) {
            case 'title':
                return preg_match('/<title[^>]*>|@section\([\'\"]title[\'\"]|@yield\([\'\"]title[\'\"]/', $content) > 0;
                
            case 'meta_description':
                return preg_match('/<meta[^>]*name=[\'"]description[\'"][^>]*>|@section\([\'\"]meta_description[\'\"]|@yield\([\'\"]meta_description[\'\"]/', $content) > 0;
                
            case 'meta_keywords':
                return preg_match('/<meta[^>]*name=[\'"]keywords[\'"][^>]*>|@section\([\'\"]meta_keywords[\'\"]|@yield\([\'\"]meta_keywords[\'\"]/', $content) > 0;
                
            case 'canonical':
                return preg_match('/<link[^>]*rel=[\'"]canonical[\'"][^>]*>|@section\([\'\"]canonical_url[\'\"]|@yield\([\'\"]canonical_url[\'\"]/', $content) > 0;
                
            case 'viewport':
                return preg_match('/<meta[^>]*name=[\'"]viewport[\'"][^>]*>/', $content) > 0;
                
            case 'language':
                return preg_match('/<html[^>]*lang=/', $content) > 0;
                
            case 'meta_robots':
                return preg_match('/<meta[^>]*name=[\'"]robots[\'"][^>]*>|@section\([\'\"]meta_robots[\'\"]|@yield\([\'\"]meta_robots[\'\"]/', $content) > 0;
                
            case 'og_title':
                return preg_match('/<meta[^>]*property=[\'"]og:title[\'"][^>]*>|@section\([\'\"]og_title[\'\"]|@yield\([\'\"]og_title[\'\"]/', $content) > 0;
                
            case 'og_description':
                return preg_match('/<meta[^>]*property=[\'"]og:description[\'"][^>]*>|@section\([\'\"]og_description[\'\"]|@yield\([\'\"]og_description[\'\"]/', $content) > 0;
                
            case 'og_image':
                return preg_match('/<meta[^>]*property=[\'"]og:image[\'"][^>]*>|@section\([\'\"]og_image[\'\"]|@yield\([\'\"]og_image[\'\"]/', $content) > 0;
                
            case 'twitter_card':
                return preg_match('/<meta[^>]*name=[\'"]twitter:card[\'"][^>]*>|@section\([\'\"]twitter_card[\'\"]|@yield\([\'\"]twitter_card[\'\"]/', $content) > 0;
                
            case 'schema_json':
                return preg_match('/<script[^>]*type=[\'"]application\/ld\+json[\'"][^>]*>|@section\([\'\"]schema[\'\"]|@yield\([\'\"]schema[\'\"]/', $content) > 0;
                
            case 'favicon':
                return preg_match('/<link[^>]*rel=[\'"]icon[\'"][^>]*>/', $content) > 0;
                
            case 'apple_touch_icon':
                return preg_match('/<link[^>]*rel=[\'"]apple-touch-icon[\'"][^>]*>/', $content) > 0;
                
            case 'preconnect':
                return preg_match('/<link[^>]*rel=[\'"]preconnect[\'"][^>]*>|<link[^>]*rel=[\'"]dns-prefetch[\'"][^>]*>/', $content) > 0;
                
            case 'language_alternates':
                return preg_match('/<link[^>]*rel=[\'"]alternate[\'"][^>]*hreflang=/', $content) > 0;
                
            case 'image_alt':
                preg_match_all('/<img[^>]*>/', $content, $images);
                foreach ($images[0] as $img) {
                    if (!preg_match('/alt=[\'"][^\'"]*[\'"]/', $img)) {
                        return false;
                    }
                }
                return true;
                
            case 'headings_hierarchy':
                return $this->checkHeadingHierarchy($content);
                
            default:
                return true;
        }
    }
    
    /**
     * Check heading hierarchy
     */
    protected function checkHeadingHierarchy(string $content): bool
    {
        preg_match_all('/<h([1-6])[^>]*>/', $content, $matches);
        if (empty($matches[1])) {
            return true; // No headings, so no hierarchy issues
        }
        
        $levels = array_map('intval', $matches[1]);
        $previousLevel = 0;
        
        foreach ($levels as $level) {
            if ($previousLevel > 0 && $level > $previousLevel + 1) {
                return false; // Skipped a level
            }
            $previousLevel = $level;
        }
        
        return true;
    }
    
    /**
     * Get severity level for an element
     */
    protected function getElementSeverity(string $elementKey): string
    {
        foreach (['A', 'AA', 'AAA'] as $level) {
            if (array_key_exists($elementKey, $this->requiredElements[$level])) {
                return $level;
            }
        }
        
        return 'AAA'; // Default to highest level
    }
    
    /**
     * Check if an issue is fixable
     */
    protected function isFixable(string $elementKey): bool
    {
        $fixableElements = [
            'viewport', 'language', 'meta_robots', 'canonical', 
            'favicon', 'preconnect', 'apple_touch_icon'
        ];
        
        return in_array($elementKey, $fixableElements);
    }
    
    /**
     * Output results as a table
     */
    protected function outputTable(): void
    {
        $this->outputEnhancedTable();
    }

    /**
     * Output enhanced table with colors and emojis
     */
    protected function outputEnhancedTable(): void
    {
        $this->line('');
        $this->line('<fg=white;options=bold>📊 SEO Audit Results:</> ');
        
        $summary = [
            'total' => count($this->results),
            'compliant' => 0,
            'issues' => 0,
            'fixable_issues' => 0
        ];
        
        $issueRows = [];
        
        foreach ($this->results as $file => $result) {
            if (empty($result['issues'])) {
                $summary['compliant']++;
                continue;
            }
            
            $summary['issues'] += count($result['issues']);
            
            foreach ($result['issues'] as $issue) {
                if ($issue['fixable']) {
                    $summary['fixable_issues']++;
                }
                
                $issueRows[] = [
                    $this->formatFileName($file),
                    $this->formatElement($issue['element']),
                    $this->formatSeverity($issue['severity']),
                    $this->formatIssueType($issue['type']),
                    $this->formatFixable($issue['fixable'])
                ];
            }
        }
        
        // Calculate compliance score
        $complianceScore = $summary['total'] > 0 ? round(($summary['compliant'] / $summary['total']) * 100, 1) : 0;
        
        // Output enhanced summary with colors
        $this->table(
            ['📋 Metric', '📊 Value'],
            [
                ['📁 Total Files', "<fg=cyan>{$summary['total']}</>"],
                ['✅ Compliant Files', "<fg=green>{$summary['compliant']}</>"],
                ['⚠️  Files with Issues', "<fg=yellow>" . ($summary['total'] - $summary['compliant']) . "</>"],
                ['🔧 Total Issues', "<fg=red>{$summary['issues']}</>"],
                ['🛠️  Fixable Issues', "<fg=blue>{$summary['fixable_issues']}</>"],
                ['📈 Compliance Score', $this->formatComplianceScore($complianceScore)]
            ]
        );
        
        if (!empty($issueRows)) {
            $this->line("\n<fg=white;options=bold>🔍 Detailed Issues:</>");
            $this->table(
                ['📄 File', '🎯 Missing Element', '⚡ Level', '🔍 Issue Type', '🔧 Fixable'],
                $issueRows
            );
            
            // Show quick fix suggestions
            if ($summary['fixable_issues'] > 0) {
                $this->line('');
                $this->line('<fg=green;options=bold>💡 Quick Fix Available!</>');
                $this->line("<fg=gray>Run:</> <fg=yellow>php artisan seo:fix --backup</> <fg=gray>to automatically fix {$summary['fixable_issues']} issues</>");
            }
        } else {
            $this->line('');
            $this->line('<fg=green;options=bold>🎉 Congratulations! All files are SEO compliant!</>');
        }
    }

    /**
     * Format file name with proper styling
     */
    protected function formatFileName(string $file): string
    {
        $shortFile = str_replace('resources/views/', '', $file);
        return "<fg=cyan>{$shortFile}</>";
    }

    /**
     * Format element name with icons
     */
    protected function formatElement(string $element): string
    {
        $icons = [
            'Title tag' => '📝',
            'Meta description' => '📄',
            'Canonical URL' => '🔗',
            'Viewport meta tag' => '📱',
            'HTML lang attribute' => '🌐',
            'Robots meta tag' => '🤖',
            'Open Graph title' => '📘',
            'Open Graph description' => '📖',
            'Open Graph image' => '🖼️',
            'Twitter card' => '🐦',
            'Schema.org JSON-LD' => '🏗️',
            'Favicon' => '⭐',
            'Apple touch icon' => '🍎',
        ];
        
        $icon = $icons[$element] ?? '🔍';
        return "{$icon} {$element}";
    }

    /**
     * Format severity level with colors
     */
    protected function formatSeverity(string $severity): string
    {
        return match($severity) {
            'A' => '<fg=red;options=bold>🚨 Critical</>',
            'AA' => '<fg=yellow;options=bold>⚠️ Important</>',
            'AAA' => '<fg=blue;options=bold>🔵 Advanced</>',
            default => $severity
        };
    }

    /**
     * Format issue type
     */
    protected function formatIssueType(string $type): string
    {
        return match($type) {
            'missing_element' => '<fg=red>❌ Missing</>',
            'invalid_format' => '<fg=yellow>⚠️ Invalid</>',
            'empty_content' => '<fg=blue>📭 Empty</>',
            default => $type
        };
    }

    /**
     * Format fixable status
     */
    protected function formatFixable(bool $fixable): string
    {
        return $fixable ? '<fg=green>✅ Yes</>' : '<fg=gray>❌ No</>';
    }

    /**
     * Format compliance score with colors
     */
    protected function formatComplianceScore(float $score): string
    {
        if ($score >= 90) {
            return "<fg=green;options=bold>🏆 {$score}% (Excellent)</>";
        } elseif ($score >= 70) {
            return "<fg=yellow;options=bold>⭐ {$score}% (Good)</>";
        } elseif ($score >= 50) {
            return "<fg=blue;options=bold>📈 {$score}% (Fair)</>";
        } else {
            return "<fg=red;options=bold>🔻 {$score}% (Needs Work)</>";
        }
    }

    /**
     * Output results as JSON
     */
    protected function outputJson(): void
    {
        $this->line(json_encode($this->results, JSON_PRETTY_PRINT));
    }

    /**
     * Check if there are fixable issues
     */
    protected function hasFixableIssues(): bool
    {
        foreach ($this->results as $result) {
            foreach ($result['issues'] ?? [] as $issue) {
                if ($issue['fixable']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Show interactive suggestions
     */
    protected function showInteractiveSuggestions(): void
    {
        $this->line('');
        $this->line('<fg=white;options=bold>💡 Interactive Fix Suggestions:</>');
        
        $fixableIssues = [];
        foreach ($this->results as $file => $result) {
            foreach ($result['issues'] ?? [] as $issue) {
                if ($issue['fixable']) {
                    $fixableIssues[] = compact('file', 'issue');
                }
            }
        }
        
        if (empty($fixableIssues)) {
            $this->line('<fg=gray>No automatically fixable issues found.</> ');
            return;
        }
        
        $choice = $this->choice(
            '🛠️ Would you like to fix these issues automatically?',
            ['Yes, fix all issues', 'Show me what will be fixed first', 'No, I\'ll fix them manually'],
            1
        );
        
        switch ($choice) {
            case 'Yes, fix all issues':
                $this->call('seo:fix', ['--backup' => true, '--path' => $this->scanPath]);
                break;
            case 'Show me what will be fixed first':
                $this->showFixPreview($fixableIssues);
                break;
            default:
                $this->line('<fg=gray>No changes made. You can run</> <fg=yellow>php artisan seo:fix --backup</> <fg=gray>when ready.</> ');
        }
    }

    /**
     * Show preview of what will be fixed
     */
    protected function showFixPreview(array $fixableIssues): void
    {
        $this->line('');
        $this->line('<fg=white;options=bold>🔍 Preview of Automatic Fixes:</>');
        
        foreach ($fixableIssues as $item) {
            $file = $item['file'];
            $issue = $item['issue'];
            
            $this->line("📄 <fg=cyan>" . str_replace('resources/views/', '', $file) . "</>");
            $this->line("   🔧 Will add: <fg=green>{$issue['element']}</>");
        }
        
        $confirm = $this->confirm("\n🚀 Proceed with these fixes?");
        
        if ($confirm) {
            $this->call('seo:fix', ['--backup' => true, '--path' => $this->scanPath]);
        } else {
            $this->line('<fg=gray>No changes made.</> ');
        }
    }
} 