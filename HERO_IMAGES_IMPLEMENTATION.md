# Dynamic Hero Images Implementation

This document outlines the implementation of dynamic hero images with admin panel upload functionality for the tour booking website.

## Overview

The dynamic hero images system allows administrators to:
- Upload and manage multiple hero images
- Set custom titles, subtitles, and descriptions for each image
- Activate/deactivate hero images
- Control the display order
- Have one active hero image displayed on the homepage

## Implementation Components

### 1. Database Structure

**Table: `hero_images`**
```sql
CREATE TABLE hero_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT NULL,
    subtitle VARCHAR(500) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    image_path VARCHAR(500) NOT NULL,
    is_active TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 2. Files Created/Modified

#### New Files:
- `admin/hero-images.php` - Admin management interface
- `admin/includes/header.php` - Shared admin header
- `admin/includes/footer.php` - Shared admin footer  
- `setup_hero_images.php` - Database setup script
- `create_hero_table.sql` - SQL script for manual setup
- `assets/images/hero/` - Directory for hero images

#### Modified Files:
- `index.php` - Updated hero section with dynamic content
- `css/style.css` - Added hero-specific styling
- `admin/index.php` - Added hero images to navigation

### 3. Setup Instructions

#### Step 1: Run Database Setup
```
http://localhost/tour/setup_hero_images.php
```
This will:
- Create the `hero_images` table
- Insert default hero data
- Create default placeholder image
- Set up necessary directories

#### Step 2: Access Admin Panel
1. Login to admin panel: `http://localhost/tour/admin/login.php`
2. Navigate to "Hero Images" in the sidebar
3. Upload your first hero image

#### Step 3: Customize Content
- Add title, subtitle, and description for your hero image
- Set sort order for multiple images
- Activate the image you want to display

## Features

### Admin Interface Features:
- **Upload Management**: Upload images with drag-and-drop interface
- **Content Management**: Set custom titles, subtitles, descriptions
- **Status Control**: Activate/deactivate images (only one can be active)
- **Sort Management**: Control display order with numeric sorting
- **Preview**: Thumbnail previews in management interface
- **Validation**: File type and size validation

### Frontend Features:
- **Responsive Design**: Hero images adapt to all screen sizes
- **Overlay Effects**: Semi-transparent overlay for text readability
- **Smooth Transitions**: CSS transitions for hover effects
- **Fallback Content**: Default content if no hero image is active
- **SEO Friendly**: Proper alt text and semantic HTML

## Technical Details

### Image Requirements:
- **Recommended Size**: 1920x1080px (16:9 aspect ratio)
- **Supported Formats**: JPEG, PNG, GIF, WebP
- **Maximum File Size**: 5MB
- **Orientation**: Landscape preferred

### CSS Classes:
- `.hero-dynamic` - Main hero container with background image
- `.hero-overlay` - Dark overlay for text contrast
- `.hero-one__content` - Content wrapper with proper z-index
- `.travhub-btn` - Styled action buttons

### Responsive Breakpoints:
- **Desktop**: Full 500px height
- **Tablet** (≤768px): 400px height, adjusted typography
- **Mobile** (≤480px): 350px height, stacked buttons

## File Structure

```
tour/
├── admin/
│   ├── hero-images.php          # Hero management interface
│   └── includes/
│       ├── header.php           # Admin header with navigation
│       └── footer.php           # Admin footer with scripts
├── assets/images/hero/          # Hero images directory
├── css/style.css               # Updated with hero styles
├── index.php                   # Updated with dynamic hero
├── setup_hero_images.php       # Setup script
└── HERO_IMAGES_IMPLEMENTATION.md # This documentation
```

## Usage Examples

### Admin Usage:
1. **Add Hero Image**:
   - Go to Admin → Hero Images
   - Fill in title, subtitle, description
   - Upload image file
   - Set sort order
   - Click "Add Hero Image"

2. **Activate Image**:
   - Find desired image in list
   - Click "Activate" button
   - Previous active image automatically deactivates

3. **Reorder Images**:
   - Change numbers in "Sort Order" column
   - Click "Update Sort Order"
   - Lower numbers display first

### Frontend Display:
The hero section automatically displays the active hero image with:
- Background image covering full section
- Overlay for text readability  
- Dynamic title, subtitle, and description
- Action buttons for tours and destinations

## Security Features

- **File Upload Validation**: Only image files allowed
- **MIME Type Detection**: Server-side file type verification
- **File Size Limits**: 5MB maximum file size
- **Authentication Required**: Admin login required for all operations
- **SQL Injection Protection**: Prepared statements used throughout

## Troubleshooting

### Common Issues:

1. **Images not displaying**:
   - Check file permissions on `assets/images/hero/` directory
   - Verify BASE_URL is correctly set in `config/config.php`
   - Ensure GD library is installed for image processing

2. **Upload failures**:
   - Check PHP `upload_max_filesize` and `post_max_size` settings
   - Verify directory write permissions
   - Check file type restrictions

3. **Database errors**:
   - Run `setup_hero_images.php` to ensure table exists
   - Check database connection in `config/config.php`
   - Verify user has CREATE and INSERT privileges

## Future Enhancements

Potential improvements:
- Multiple hero images carousel/slider
- Image compression and optimization
- Crop/resize functionality
- Scheduled activation/deactivation
- Hero image analytics
- Video hero backgrounds
- Animation effects

## Support

For issues or questions regarding the hero images implementation:
1. Check this documentation first
2. Verify all setup steps completed
3. Check server error logs
4. Test with different image files

The implementation is complete and ready for use!
