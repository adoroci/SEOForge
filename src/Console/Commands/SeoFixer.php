<?php

namespace ProbeForge\SEOForge\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SeoFixer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:fix 
                            {--path=resources/views : Path to fix SEO issues in blade files}
                            {--backup : Create backups before modifying files}
                            {--issues=* : Specific issues to fix (viewport,canonical,meta_robots,language,etc.)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '🛠️ Add basic SEO tags to templates - content creation not included (alpha)';

    /**
     * Known fixes we can apply
     */
    protected array $fixableIssues = [
        'viewport' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">",
            'description' => '📱 Adds responsive viewport meta tag',
            'priority' => 'high'
        ],
        'language' => [
            'pattern' => '/<html[^>]*>/',
            'replacement' => '<html lang="en" data-bs-theme="dark">',
            'description' => '🌐 Sets HTML language attribute',
            'priority' => 'high'
        ],
        'meta_robots' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <meta name=\"robots\" content=\"index, follow\">",
            'description' => '🤖 Adds search engine robots directive',
            'priority' => 'medium'
        ],
        'canonical' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <link rel=\"canonical\" href=\"{{ request()->url() }}\">",
            'description' => '🔗 Adds canonical URL for duplicate content prevention',
            'priority' => 'high'
        ],
        'favicon' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <link rel=\"icon\" type=\"image/x-icon\" href=\"{{ asset('favicon.ico') }}\">",
            'description' => '⭐ Adds favicon link',
            'priority' => 'low'
        ],
        'preconnect' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <link rel=\"preconnect\" href=\"{{ config('app.url') }}\">\n    <link rel=\"dns-prefetch\" href=\"{{ config('app.url') }}\">",
            'description' => '🚀 Adds DNS prefetch and preconnect hints',
            'priority' => 'medium'
        ],
        'apple_touch_icon' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"{{ asset('apple-touch-icon.png') }}\">",
            'description' => '🍎 Adds Apple touch icon',
            'priority' => 'low'
        ],
        'missing_alt' => [
            'pattern' => '/<img([^>]*)(?!alt=)([^>]*)>/',
            'replacement' => '<img$1 alt="Image"$2>',
            'description' => '🖼️ Adds alt attributes to images',
            'priority' => 'high'
        ],
    ];

    /**
     * Path to fix
     */
    protected string $fixPath;
    
    /**
     * Create backups
     */
    protected bool $createBackups;
    
    /**
     * Issues to fix
     */
    protected array $issuesToFix;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->fixPath = $this->option('path');
        $this->createBackups = $this->option('backup');
        $this->issuesToFix = $this->option('issues');
        
        // Show welcome banner
        $this->showWelcomeBanner();
        
        if (!File::exists($this->fixPath)) {
            $this->error("❌ Path not found: {$this->fixPath}");
            return 1;
        }

        // If no issues specified, ask which ones to fix
        if (empty($this->issuesToFix)) {
            $this->issuesToFix = $this->askWhichIssuesToFix();
        }
        
        // Validate the issues to fix
        foreach ($this->issuesToFix as $issue) {
            if (!array_key_exists($issue, $this->fixableIssues)) {
                $this->error("❌ Unknown issue: <fg=yellow>{$issue}</>");
                return 1;
            }
        }

        $this->info("🛠️ Starting SEO fixes...");
        $this->line("<fg=gray>📂 Target path: {$this->fixPath}</>");
        
        if ($this->createBackups) {
            $this->line("<fg=green>💾 Backup mode enabled - your files are safe!</>");
        } else {
            $this->warn("⚠️ No backup mode - changes will be made directly to files");
        }
        
        $this->fixFiles();
        
        return 0;
    }

    /**
     * Show welcome banner
     */
    protected function showWelcomeBanner(): void
    {
        $this->line('');
        $this->line('<fg=green>╔══════════════════════════════════════════════╗</>');
        $this->line('<fg=green>║</> <fg=white;options=bold>🛠️ SEOForge - Basic SEO Fixer (Alpha)</> <fg=green>║</>');
        $this->line('<fg=green>║</> <fg=gray>Adds basic tags only - write content yourself</> <fg=green>║</>');
        $this->line('<fg=green>╚══════════════════════════════════════════════╝</>');
        $this->line('');
    }
    
    /**
     * Ask which issues to fix with enhanced interface
     */
    protected function askWhichIssuesToFix(): array
    {
        $this->line('<fg=white;options=bold>🔧 Available SEO Fixes:</>');
        $this->line('');
        
        $choices = [];
        foreach ($this->fixableIssues as $key => $fix) {
            $priority = $this->formatPriority($fix['priority']);
            $this->line("  {$priority} <fg=cyan>{$key}</> - {$fix['description']}");
            $choices[] = $key;
        }
        
        $this->line('');
        return $this->choice(
            '🎯 Which issues would you like to fix?',
            array_merge(['all'], $choices),
            null,
            null,
            true
        ) === ['all'] ? $choices : $this->choice(
            '🎯 Which issues would you like to fix?',
            $choices,
            null,
            null,
            true
        );
    }

    /**
     * Format priority with colors and icons
     */
    protected function formatPriority(string $priority): string
    {
        return match($priority) {
            'high' => '<fg=red;options=bold>🚨 HIGH</> ',
            'medium' => '<fg=yellow;options=bold>⚠️ MED </> ',
            'low' => '<fg=blue;options=bold>🔵 LOW </> ',
            default => '🔍 UNK  '
        };
    }
    
    /**
     * Fix SEO issues in blade files with enhanced reporting
     */
    protected function fixFiles(): void
    {
        $files = $this->getBladeFiles();
        $this->info("📄 Found <fg=yellow>" . count($files) . "</> blade files to process");
        
        $bar = $this->output->createProgressBar(count($files));
        $bar->setFormat('🔄 Fixing: %current%/%max% [%bar%] %percent:3s%% %message%');
        $bar->start();
        
        $fixed = 0;
        $errors = 0;
        $totalFixes = 0;
        
        foreach ($files as $file) {
            $fileName = basename($file);
            $bar->setMessage("Processing: <fg=cyan>{$fileName}</>");
            
            try {
                $result = $this->fixFile($file);
                if ($result['changed']) {
                    $fixed++;
                    $totalFixes += $result['fixes_applied'];
                }
            } catch (\Exception $e) {
                $this->line("\n<fg=red>❌ Error fixing file {$fileName}: " . $e->getMessage() . "</>");
                $errors++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("\n");
        
        // Enhanced completion summary
        $this->showCompletionSummary($fixed, $errors, $totalFixes, count($files));
    }
    
    /**
     * Get all blade files in the specified path
     */
    protected function getBladeFiles(): array
    {
        $files = [];
        
        // Check if the path is a specific file
        if (is_file($this->fixPath) && str_ends_with($this->fixPath, '.blade.php')) {
            return [$this->fixPath];
        }
        
        // If it's a directory, scan recursively
        if (is_dir($this->fixPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->fixPath, \RecursiveDirectoryIterator::SKIP_DOTS)
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
     * Fix a single file
     */
    protected function fixFile(string $file): array
    {
        $relativePath = str_replace(base_path() . '/', '', $file);
        $content = File::get($file);
        $originalContent = $content;
        $changed = false;
        $fixesApplied = 0;
        
        // Create backup if requested
        if ($this->createBackups) {
            $this->createBackup($file);
        }
        
        // Apply each fix
        foreach ($this->issuesToFix as $issue) {
            if (!isset($this->fixableIssues[$issue])) {
                continue;
            }
            
            $fix = $this->fixableIssues[$issue];
            
            // Apply the fix if it doesn't already exist
            if ($issue === 'missing_alt') {
                // Special handling for alt tags
                $imgTags = [];
                preg_match_all('/<img[^>]*>/', $content, $imgTags);
                
                if (!empty($imgTags[0])) {
                    foreach ($imgTags[0] as $imgTag) {
                        if (!preg_match('/alt=[\'"][^\'"]*[\'"]/', $imgTag)) {
                            $newImgTag = preg_replace('/<img([^>]*)>/', '<img$1 alt="Image">', $imgTag);
                            $content = str_replace($imgTag, $newImgTag, $content);
                            $changed = true;
                            $fixesApplied++;
                        }
                    }
                }
            } else {
                // Regular pattern replacement
                if (!$this->isAlreadyFixed($content, $issue)) {
                    $newContent = preg_replace($fix['pattern'], $fix['replacement'], $content, 1);
                    if ($newContent !== $content) {
                        $content = $newContent;
                        $changed = true;
                        $fixesApplied++;
                    }
                }
            }
        }
        
        // Write changes back to file
        if ($changed) {
            File::put($file, $content);
            Log::info("SEO fixes applied to {$relativePath}", [
                'issues_fixed' => $this->issuesToFix,
                'backup_created' => $this->createBackups
            ]);
        }
        
        return [
            'changed' => $changed,
            'fixes_applied' => $fixesApplied
        ];
    }
    
    /**
     * Create a backup of the file
     */
    protected function createBackup(string $file): void
    {
        $backupPath = config('seo-audit.fixes.backup_path', storage_path('app/seo-backups'));
        
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }
        
        // Fix path separators for Windows compatibility
        $relativePath = str_replace([base_path() . '/', base_path() . '\\'], '', $file);
        $backupFileName = str_replace(['/', '\\', ':'], '_', $relativePath) . '_' . date('Y-m-d_H-i-s') . '.backup';
        $backupFile = $backupPath . DIRECTORY_SEPARATOR . $backupFileName;
        
        File::copy($file, $backupFile);
    }
    
    /**
     * Check if an issue is already fixed
     */
    protected function isAlreadyFixed(string $content, string $issue): bool
    {
        switch ($issue) {
            case 'viewport':
                return preg_match('/<meta[^>]*name=[\'"]viewport[\'"][^>]*>/', $content) > 0;
            case 'language':
                return preg_match('/<html[^>]*lang=/', $content) > 0;
            case 'meta_robots':
                return preg_match('/<meta[^>]*name=[\'"]robots[\'"][^>]*>/', $content) > 0;
            case 'canonical':
                return preg_match('/<link[^>]*rel=[\'"]canonical[\'"][^>]*>/', $content) > 0;
            case 'favicon':
                return preg_match('/<link[^>]*rel=[\'"]icon[\'"][^>]*>/', $content) > 0;
            case 'preconnect':
                return preg_match('/<link[^>]*rel=[\'"]preconnect[\'"][^>]*>|<link[^>]*rel=[\'"]dns-prefetch[\'"][^>]*>/', $content) > 0;
            case 'apple_touch_icon':
                return preg_match('/<link[^>]*rel=[\'"]apple-touch-icon[\'"][^>]*>/', $content) > 0;
            default:
                return false;
        }
    }

    /**
     * Show completion summary
     */
    protected function showCompletionSummary(int $fixed, int $errors, int $totalFixes, int $totalFiles): void
    {
        $this->line("\n");
        $this->info("🎉 Fix complete: {$fixed} files modified, {$errors} errors, {$totalFixes} fixes applied");
        $this->line("<fg=gray>📄 Total files processed: {$totalFiles}</>");
    }
} 