<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixStorageSymlink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:storage-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates or fixes the storage symlink in shared hosting environments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetPath = storage_path('app/public');
        $linkPath = public_path('storage');

        // Ensure target directory exists
        if (!File::exists($targetPath)) {
            File::makeDirectory($targetPath, 0755, true);
            $this->info("Created storage directory: {$targetPath}");
        }

        // Remove existing link or directory if it exists
        if (File::exists($linkPath)) {
            if (is_link($linkPath)) {
                // It's a symlink, unlink it
                unlink($linkPath);
                $this->info("Removed existing symlink: {$linkPath}");
            } else {
                // It's a directory, remove it
                File::deleteDirectory($linkPath);
                $this->info("Removed existing directory: {$linkPath}");
            }
        }

        // Create symlink
        try {
            if (File::link($targetPath, $linkPath)) {
                $this->info("Storage link has been created: {$linkPath} -> {$targetPath}");
                return 0;
            } else {
                $this->error("Failed to create the symbolic link.");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Exception while creating symlink: " . $e->getMessage());
            
            // Fallback to copy method for shared hosting
            $this->info("Trying alternative method for shared hosting...");
            
            // Create a PHP file in public/storage.php that serves files from storage/app/public
            $fallbackContent = <<<'PHP'
<?php
// This is a fallback for environments where symlinks aren't supported
$path = isset($_GET['path']) ? $_GET['path'] : null;

if (!$path) {
    http_response_code(404);
    echo "File not found";
    exit;
}

// Prevent directory traversal
$path = str_replace('..', '', $path);
$fullPath = __DIR__ . '/../storage/app/public/' . $path;

if (!file_exists($fullPath)) {
    http_response_code(404);
    echo "File not found";
    exit;
}

// Get file info
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $fullPath);
finfo_close($finfo);

// Set headers and output file
header('Content-Type: ' . $mime);
readfile($fullPath);
PHP;
            
            // Create directory structure to mirror storage/app/public
            if (!File::exists($linkPath)) {
                File::makeDirectory($linkPath, 0755, true);
            }
            
            // Create the fallback PHP file
            File::put(public_path('storage.php'), $fallbackContent);
            $this->info("Created fallback storage.php file");
            
            // Create an .htaccess file to rewrite requests
            $htaccessContent = <<<'HTACCESS'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^storage/(.*)$ storage.php?path=$1 [L]
</IfModule>
HTACCESS;
            
            File::put(public_path('.htaccess'), $htaccessContent, FILE_APPEND);
            $this->info("Updated .htaccess with storage rewrite rules");
            
            return 0;
        }
    }
} 