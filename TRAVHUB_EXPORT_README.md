# TravHub Tour Booking System - Complete Export

## 📦 Export Contents

This export contains the complete TravHub Tour Booking System with all code and database files needed for deployment.

### Files Included:
1. `tour_database_export.sql` - Complete database structure and sample data
2. `tour_code_export_fixed.zip` - All PHP application files (URL-fixed version)
3. `TRAVHUB_EXPORT_README.md` - This setup guide
4. `URL_CONFIGURATION_NOTES.txt` - URL configuration documentation

---

## 🛠️ System Requirements

- **Web Server**: Apache/Nginx
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Extensions**: PDO, PDO_MySQL, GD, JSON

---

## 📋 Installation Instructions

### Step 1: Database Setup
1. Open your MySQL client (phpMyAdmin, MySQL Workbench, or command line)
2. Import the database:
   ```sql
   SOURCE tour_database_export.sql;
   ```
   OR in phpMyAdmin: Import → Choose `tour_database_export.sql` → Go

### Step 2: Code Deployment
1. Extract `tour_code_export_fixed.zip` to your web server directory
2. Set proper permissions:
   - `uploads/` folder: 755 (writable)
   - `assets/images/` folder: 755 (writable)

### Step 3: Configuration
1. Update database connection in `config/database.php`:
   ```php
   private $host = 'your_host';        // Usually 'localhost'
   private $database = 'travhub_db';   // Database name
   private $username = 'your_username'; // Your DB username
   private $password = 'your_password'; // Your DB password
   ```

### Step 4: File Permissions
Set these folders to be writable (755 or 777):
```bash
chmod 755 uploads/
chmod 755 uploads/tours/
chmod 755 uploads/destinations/
chmod 755 uploads/blogs/
chmod 755 assets/images/hero/
chmod 755 assets/images/tours/
chmod 755 assets/images/destinations/
```

---

## 🔐 Default Admin Access

**Admin Login URL**: `your-domain.com/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`
- **Email**: `admin@travhub.com`

⚠️ **IMPORTANT**: Change the default admin password after first login!

---

## 📁 Directory Structure

```
tour/
├── admin/                  # Admin panel
│   ├── login.php          # Admin login
│   ├── index.php          # Dashboard
│   ├── tours.php          # Tour management
│   ├── bookings.php       # Booking management
│   ├── destinations.php   # Destination management
│   └── includes/          # Admin includes
├── assets/                # Static assets
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   ├── images/           # Images
│   └── vendors/          # Third-party libraries
├── config/               # Configuration files
│   ├── database.php      # Database configuration
│   └── setup_database.sql
├── includes/             # PHP includes
│   ├── header.php        # Site header
│   ├── footer.php        # Site footer
│   └── db.php           # Database helper
├── uploads/              # User uploads
│   ├── tours/           # Tour images
│   ├── destinations/    # Destination images
│   └── blogs/           # Blog images
├── index.php             # Homepage
├── tours.php            # Tours listing
├── tour-details.php     # Tour details page
├── destinations.php     # Destinations page
├── booking.php          # Booking form
├── contact.php          # Contact page
├── login.php            # User login
├── register.php         # User registration
└── .htaccess            # Apache configuration
```

---

## 🔧 URL Configuration (FIXED!)

**✅ No More Hardcoded URLs!**

This export has been updated to automatically detect the correct URLs on any server:

- **Dynamic BASE_URL Detection**: Automatically detects HTTP/HTTPS, domain, and directory path
- **No Manual URL Changes Needed**: Works on any domain without modification
- **Responsive to Server Environment**: Adapts to different hosting environments

### How It Works:
1. The system detects the protocol (HTTP/HTTPS) automatically
2. Uses the current domain name from server variables
3. Auto-detects the application directory path
4. All internal links use the dynamically generated BASE_URL

### What Was Fixed:
- Removed hardcoded `localhost/tour` URLs from config.php
- Updated logo URL to use dynamic path
- Made all navigation links relative to BASE_URL
- Fixed admin redirects to use dynamic URLs

**Result**: Your application will work immediately on any domain without URL issues!

---

## 🌟 Key Features

### Frontend Features:
- **Responsive Design**: Works on all devices
- **Tour Browsing**: Search and filter tours
- **Booking System**: Online tour booking
- **User Registration**: User accounts and profiles
- **Payment Integration**: Ready for payment gateway
- **Contact Forms**: Contact and inquiry forms
- **Blog System**: Travel blog functionality

### Admin Features:
- **Dashboard**: Admin overview and statistics
- **Tour Management**: Add, edit, delete tours
- **Booking Management**: Manage customer bookings
- **Destination Management**: Manage tour destinations
- **User Management**: Manage registered users
- **Content Management**: Manage site content
- **Hero Image Management**: Manage homepage slides
- **Settings**: Site configuration options

---

## 🗄️ Database Tables

The database contains 15 tables:
1. `admin_users` - Admin user accounts
2. `site_settings` - Site configuration
3. `destinations` - Tour destinations
4. `tour_categories` - Tour categories
5. `tours` - Tour packages
6. `tour_category_relations` - Many-to-many relations
7. `bookings` - Customer bookings
8. `addons` - Tour add-ons
9. `booking_addons` - Booking add-on relations
10. `users` - Frontend user accounts
11. `hero_images` - Homepage hero images
12. `blog_posts` - Blog articles
13. `contact_messages` - Contact form submissions
14. `cab_pricing` - Vehicle pricing
15. Various indexes for performance

---

## 🔧 Configuration Options

### Site Settings (Admin Panel):
- Site Name: TravHub
- Site Tagline: Adventure & Experience The Travel
- Contact Email: exam126@gmail.com
- Phone: +1 234 567 8900
- Address: 6391 Elgin St. Celina, Delaware 10299
- Opening Hours: 9:00am - 10:00pm

### Sample Data Included:
- **4 Sample Destinations**: Shimla, Manali, Dharamshala, Kasol
- **4 Sample Tours**: Hill station, Adventure, Spiritual, Backpacking
- **5 Sample Add-ons**: Paragliding, Rafting, Trekking, Photography, Camping
- **5 Vehicle Types**: Sedan, SUV, Tempo Traveller, Mini Bus, Luxury Car

---

## 🚀 Post-Installation Steps

1. **Security**:
   - Change default admin password
   - Update database credentials
   - Set proper file permissions
   - Configure SSL certificate

2. **Customization**:
   - Upload your logo to `assets/images/`
   - Update site settings in admin panel
   - Add your tour packages
   - Configure payment gateway

3. **SEO Setup**:
   - Update meta titles and descriptions
   - Set up Google Analytics
   - Configure robots.txt
   - Submit sitemap to search engines

---

## 🆘 Troubleshooting

### Common Issues:

**1. Database Connection Error**
- Check `config/database.php` credentials
- Ensure MySQL service is running
- Verify database exists

**2. Image Upload Issues**
- Check folder permissions (755 or 777)
- Verify PHP upload limits
- Ensure GD extension is enabled

**3. Admin Login Issues**
- Verify admin user exists in database
- Check password hash
- Clear browser cache

**4. URL Rewriting Issues**
- Ensure `.htaccess` is uploaded
- Check Apache mod_rewrite is enabled
- Verify server supports URL rewriting

---

## 📞 Support Information

This is a complete, self-contained tour booking system. All necessary files and database structure are included.

### System Specifications:
- **Framework**: Pure PHP (No framework dependencies)
- **Database**: MySQL with PDO
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap
- **Admin Panel**: PHP-based admin interface
- **File Upload**: Built-in image upload system
- **Security**: Password hashing, SQL injection protection

### File Sizes:
- Database Export: ~50KB
- Code Archive: Varies based on assets
- Total System: Complete and ready to deploy

---

## ✅ Deployment Checklist

- [ ] Extract code files to web directory
- [ ] Import database from SQL file
- [ ] Update database configuration
- [ ] Set file permissions
- [ ] Test admin login
- [ ] Test frontend functionality
- [ ] Configure payment gateway (if needed)
- [ ] Update site settings
- [ ] Add SSL certificate
- [ ] Set up backups

---

**🎉 Your TravHub Tour Booking System is ready to go!**

*Generated on: 2025-10-01*