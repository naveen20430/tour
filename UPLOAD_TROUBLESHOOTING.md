# Hero Image Upload Troubleshooting

## 🔍 Problem: Failed to Upload Image in Admin Section

I've created several diagnostic tools to identify and fix the upload issue:

## 🛠️ Diagnostic Tools Created:

### 1. **Database Setup Script**
**File**: `create_hero_table.php`
**Purpose**: Creates the hero_images table and sets up directories
**URL**: `http://localhost/tour/create_hero_table.php`

### 2. **Upload Test (No Database)**
**File**: `test_upload_only.php`
**Purpose**: Tests pure file upload functionality without database
**URL**: `http://localhost/tour/test_upload_only.php`

### 3. **Debug Admin Page**
**File**: `admin/hero-images-debug.php`
**Purpose**: Full admin page with detailed debug information
**URL**: `http://localhost/tour/admin/hero-images-debug.php`

## 🎯 Most Likely Issues:

### 1. **Database Table Missing**
- The `hero_images` table might not exist
- **Solution**: Run `create_hero_table.php`

### 2. **Directory Permissions**
- The `assets/images/hero/` directory might not exist or be writable
- **Solution**: The scripts will create and fix permissions

### 3. **PHP Upload Settings**
- File size limits too small
- Uploads disabled
- **Check**: `upload_max_filesize`, `post_max_size`, `file_uploads`

### 4. **File Type Validation**
- Only image files are allowed
- Check the `getUploadError()` function

## 📋 Step-by-Step Troubleshooting:

### Step 1: Check Database Setup
1. Visit: `http://localhost/tour/create_hero_table.php`
2. This will create the table and directory if needed
3. Check if it shows any errors

### Step 2: Test Basic Upload
1. Visit: `http://localhost/tour/test_upload_only.php`
2. Try uploading a small image (under 2MB)
3. Check the debug information shown

### Step 3: Use Debug Admin Page
1. Visit: `http://localhost/tour/admin/hero-images-debug.php`
2. Login to admin if needed
3. Try uploading through the debug interface
4. Check all debug messages

## 🔧 Common Fixes:

### Fix 1: Create Missing Directory
```bash
# If directory doesn't exist, create it with proper permissions
mkdir -p E:\desktop\htdocs\tour\assets\images\hero
chmod 755 E:\desktop\htdocs\tour\assets\images\hero
```

### Fix 2: Database Connection
- Make sure your database is running
- Check database credentials in `config/database.php`

### Fix 3: PHP Settings (php.ini)
```ini
file_uploads = On
upload_max_filesize = 10M
post_max_size = 12M
max_file_uploads = 20
```

### Fix 4: File Size
- Try uploading a very small image first (under 1MB)
- If that works, the issue is file size limits

## 🧪 Testing Sequence:

1. **Visit `create_hero_table.php`** - Sets up everything needed
2. **Visit `test_upload_only.php`** - Tests upload without complexity
3. **Visit `admin/hero-images-debug.php`** - Full admin test with debug info
4. **Visit `admin/hero-images.php`** - Normal admin page

## 📱 Expected Results:

### If Successful:
- ✅ Table created/exists
- ✅ Directory exists and writable
- ✅ File uploads successfully
- ✅ Image appears in hero section

### If Issues Found:
- ❌ Database connection error → Check MySQL service
- ❌ Directory not writable → Fix permissions
- ❌ File too large → Reduce file size or increase PHP limits
- ❌ Invalid file type → Use JPG, PNG, GIF, or WebP

## 🚀 Quick Fix Commands:

If you have command line access:

```bash
# Navigate to your project
cd E:\desktop\htdocs\tour

# Create hero directory with permissions
mkdir -p assets\images\hero
chmod 755 assets\images\hero

# Check if PHP allows uploads
php -r "echo 'Upload enabled: ' . (ini_get('file_uploads') ? 'YES' : 'NO') . PHP_EOL;"
php -r "echo 'Max file size: ' . ini_get('upload_max_filesize') . PHP_EOL;"
```

## 📞 Next Steps:

1. **Run the diagnostic tools in order**
2. **Check the debug output carefully**
3. **Look for specific error messages**
4. **Try with a very small test image first**

The debug tools will show exactly what's wrong and help fix the upload issue!
