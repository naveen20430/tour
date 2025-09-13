<?php
require_once 'config/config.php';

echo "<h2>File Upload Test (No Database)</h2>";

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h3>Upload Attempt:</h3>";
    
    // Show file details
    echo "<p><strong>File Details:</strong></p>";
    echo "<pre>" . print_r($_FILES['test_file'], true) . "</pre>";
    
    // Check upload errors
    if ($_FILES['test_file']['error'] !== 0) {
        switch ($_FILES['test_file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $errors[] = 'File is too large (php.ini limit: ' . ini_get('upload_max_filesize') . ')';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'File is too large (form limit)';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors[] = 'File upload was interrupted';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = 'No file was selected';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $errors[] = 'Server error: no temp directory';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $errors[] = 'Server error: cannot write file';
                break;
            default:
                $errors[] = 'Unknown upload error: ' . $_FILES['test_file']['error'];
        }
    } else {
        // Try to use the uploadFile function
        $result = uploadFile($_FILES['test_file'], 'hero');
        
        if ($result) {
            $success = "Upload successful! File saved as: $result";
            $fullPath = BASE_PATH . $result;
            
            if (file_exists($fullPath)) {
                $success .= " (File verified to exist at: $fullPath)";
            } else {
                $errors[] = "Upload function returned success but file doesn't exist at: $fullPath";
            }
        } else {
            $errors[] = "uploadFile() function returned false";
            
            // Check upload errors manually
            $uploadErrors = getUploadError($_FILES['test_file']);
            if (!empty($uploadErrors)) {
                $errors = array_merge($errors, $uploadErrors);
            }
        }
    }
}

// Check system status
echo "<h3>System Status:</h3>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><td><strong>PHP Version</strong></td><td>" . phpversion() . "</td></tr>";
echo "<tr><td><strong>Upload Max Filesize</strong></td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td><strong>Post Max Size</strong></td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td><strong>Max File Uploads</strong></td><td>" . ini_get('max_file_uploads') . "</td></tr>";
echo "<tr><td><strong>File Uploads Enabled</strong></td><td>" . (ini_get('file_uploads') ? 'YES' : 'NO') . "</td></tr>";
echo "</table>";

echo "<h3>Directory Status:</h3>";
$heroDir = BASE_PATH . 'assets/images/hero/';
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><td><strong>BASE_PATH</strong></td><td>" . BASE_PATH . "</td></tr>";
echo "<tr><td><strong>Hero Directory</strong></td><td>" . $heroDir . "</td></tr>";
echo "<tr><td><strong>Directory Exists</strong></td><td>" . (is_dir($heroDir) ? 'YES' : 'NO') . "</td></tr>";
echo "<tr><td><strong>Directory Writable</strong></td><td>" . (is_writable($heroDir) ? 'YES' : 'NO') . "</td></tr>";
echo "<tr><td><strong>Parent Dir Writable</strong></td><td>" . (is_writable(dirname($heroDir)) ? 'YES' : 'NO') . "</td></tr>";
echo "</table>";

// Create directory if it doesn't exist
if (!is_dir($heroDir)) {
    if (mkdir($heroDir, 0755, true)) {
        echo "<p style='color: green;'>✓ Created hero directory</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create hero directory</p>";
    }
}

// Try to fix permissions
if (is_dir($heroDir) && !is_writable($heroDir)) {
    if (chmod($heroDir, 0755)) {
        echo "<p style='color: green;'>✓ Fixed directory permissions</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to fix directory permissions</p>";
    }
}

if ($success) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; margin: 10px 0;'>";
    echo "<h4>Success!</h4>";
    echo "<p>$success</p>";
    echo "</div>";
}

if (!empty($errors)) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; margin: 10px 0;'>";
    echo "<h4>Errors:</h4>";
    foreach ($errors as $error) {
        echo "<p>• $error</p>";
    }
    echo "</div>";
}
?>

<h3>Test Upload:</h3>
<form method="POST" enctype="multipart/form-data" style="border: 1px solid #ccc; padding: 20px; margin: 10px 0;">
    <div style="margin-bottom: 15px;">
        <label for="test_file"><strong>Select Image File:</strong></label><br>
        <input type="file" name="test_file" id="test_file" accept="image/*" required>
        <small style="color: #666;">Max size: <?php echo ini_get('upload_max_filesize'); ?></small>
    </div>
    
    <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
        Test Upload
    </button>
</form>

<p><a href="admin/hero-images-debug.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Go to Debug Admin Page</a></p>
