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
    protected $description = 'Fix common SEO issues in blade templates';

    /**
     * Known fixes we can apply
     */
    protected array $fixableIssues = [
        'viewport' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">",
        ],
        'language' => [
            'pattern' => '/<html[^>]*>/',
            'replacement' => '<html lang="en" data-bs-theme="dark">',
        ],
        'meta_robots' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <meta name=\"robots\" content=\"index, follow\">",
        ],
        'canonical' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <link rel=\"canonical\" href=\"{{ request()->url() }}\">",
        ],
        'favicon' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <link rel=\"icon\" type=\"image/x-icon\" href=\"{{ asset('favicon.ico') }}\">",
        ],
        'preconnect' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <link rel=\"preconnect\" href=\"{{ config('app.url') }}\">\n    <link rel=\"dns-prefetch\" href=\"{{ config('app.url') }}\">",
        ],
        'apple_touch_icon' => [
            'pattern' => '/<head[^>]*>/',
            'replacement' => "<head>\n    <link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"{{ asset('apple-touch-icon.png') }}\">",
        ],
        'missing_alt' => [
            'pattern' => '/<img([^>]*)(?!alt=)([^>]*)>/',
            'replacement' => '<img$1 alt="Image"$2>',
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
        
        if (!File::exists($this->fixPath)) {
            $this->error("Path not found: {$this->fixPath}");
            return 1;
        }

        // If no issues specified, ask which ones to fix
        if (empty($this->issuesToFix)) {
            $this->issuesToFix = $this->askWhichIssuesToFix();
        }
        
        // Validate the issues to fix
        foreach ($this->issuesToFix as $issue) {
            if (!array_key_exists($issue, $this->fixableIssues)) {
                $this->error("Unknown issue: {$issue}");
                return 1;
            }
        }

        $this->info("Starting SEO fixes...");
        $this->fixFiles();
        
        return 0;
    }
    
    /**
     * Ask which issues to fix
     */
    protected function askWhichIssuesToFix(): array
    {
        $choices = array_keys($this->fixableIssues);
        
        return $this->choice(
            'Which issues would you like to fix?',
            $choices,
            null,
            null,
            true
        );
    }
    
    /**
     * Fix SEO issues in blade files
     */
    protected function fixFiles(): void
    {
        $files = $this->getBladeFiles();
        $this->info("Found " . count($files) . " blade files to process");
        
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();
        
        $fixed = 0;
        $errors = 0;
        
        foreach ($files as $file) {
            try {
                $result = $this->fixFile($file);
                if ($result) {
                    $fixed++;
                }
            } catch (\Exception $e) {
                $this->line("\nError fixing file {$file}: " . $e->getMessage());
                $errors++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("\n");
        
        $this->info("Fix complete: {$fixed} files modified, {$errors} errors");
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
    protected function fixFile(string $file): bool
    {
        $relativePath = str_replace(base_path() . '/', '', $file);
        $content = File::get($file);
        $originalContent = $content;
        $changed = false;
        
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
        
        return $changed;
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
        
        $backupFile = $backupPath . '/' . str_replace('/', '_', $file) . '_' . date('Y-m-d_H-i-s') . '.backup';
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
} 