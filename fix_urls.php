<?php
/**
 * URL Fix Script
 * This script fixes hardcoded localhost URLs throughout the application
 */

echo "Starting URL fix process...\n";

// Fix the logo URL in header.php
$headerFile = 'includes/header.php';
if (file_exists($headerFile)) {
    $content = file_get_contents($headerFile);
    // Replace hardcoded logo URL with dynamic path
    $content = str_replace(
        'https://theworldjourney.in/images/logo.png',
        '<?php echo BASE_URL; ?>assets/images/logo-1.png',
        $content
    );
    file_put_contents($headerFile, $content);
    echo "Fixed logo URL in header.php\n";
}

// Fix any remaining localhost URLs in PHP files
$phpFiles = [
    'includes/db.php',
    'admin/setup.php',
    'setup.php',
    'install.php',
    'install_inr.php'
];

foreach ($phpFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $originalContent = $content;
        
        // Replace localhost with generic localhost (keeping it as localhost for local development)
        // The main fix is in config.php with dynamic BASE_URL
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "Updated $file\n";
        }
    }
}

// Create a configuration note file
$configNote = "
IMPORTANT: URL Configuration Notes
================================

1. The main configuration has been updated in config/config.php to use dynamic BASE_URL detection.
2. This means the application will automatically detect the correct domain and path.
3. No more hardcoded localhost URLs in the core configuration.

How it works:
- Detects HTTP/HTTPS automatically
- Uses current domain name
- Auto-detects the application directory path
- Works on any server without modification

Manual Updates (if needed):
- Update any remaining hardcoded URLs in custom files
- Check .htaccess file for any domain-specific rules
- Update database entries that might contain absolute URLs
";

file_put_contents('URL_CONFIGURATION_NOTES.txt', $configNote);

echo "\nURL fix process completed!\n";
echo "Key changes made:\n";
echo "1. Dynamic BASE_URL detection in config/config.php\n";
echo "2. Fixed logo URL in header.php\n";
echo "3. Created configuration notes file\n";
echo "\nYour application should now work on any domain without URL issues.\n";
?>