# Deployment Guide - AB Bau Fliesen

## Pre-Deployment Checklist

### 1. Database Configuration
- [ ] Update `admin/includes/db_connect.php` with production database credentials
- [ ] Create production database and import schema
- [ ] Test database connection
- [ ] Backup existing database (if updating)

### 2. File Cleanup
- [x] Remove temporary files (`last_otp.txt`, `about copy.php`)
- [x] Remove documentation files (optional)
- [x] Clean up `.gitignore` file

### 3. Configuration Updates
- [ ] Update database credentials in `admin/includes/db_connect.php`
- [ ] Verify all paths are relative (not hardcoded localhost)
- [ ] Check `.htaccess` file for proper configuration
- [ ] Verify `site.webmanifest` paths are correct

### 4. Security
- [ ] Change default admin password
- [ ] Verify file permissions (uploads/ folder should be writable)
- [ ] Check `.htaccess` for proper security rules
- [ ] Ensure sensitive files are not accessible via web

### 5. Testing
- [ ] Test admin panel login
- [ ] Test user creation/editing/deletion
- [ ] Test image uploads
- [ ] Test all CRUD operations
- [ ] Test 2FA functionality
- [ ] Test frontend pages load correctly
- [ ] Test API endpoints

## Production Environment Variables

You can set these environment variables on your server:

```bash
DB_HOST=your_database_host
DB_USER=your_database_user
DB_PASS=your_database_password
DB_NAME=your_database_name
```

Or update `admin/includes/db_connect.php` directly with production credentials.

## File Structure

```
ab-bau/
├── admin/              # Admin panel
│   ├── includes/
│   │   └── db_connect.php  # Database configuration
│   └── ...
├── api/                # API endpoints
├── uploads/             # User uploaded files (must be writable)
├── assets/              # Static assets
├── css/                 # Stylesheets
├── js/                  # JavaScript files
└── *.html               # Public pages
```

## Server Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB equivalent)
- Apache with mod_rewrite enabled
- GD Library for image processing
- Write permissions on `uploads/` folder

## Deployment Steps

1. **Upload Files**
   ```bash
   # Upload all files to your server
   # Exclude node_modules/ and other development files
   ```

2. **Set Permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 admin/includes/db_connect.php
   ```

3. **Configure Database**
   - Update `admin/includes/db_connect.php` with production credentials
   - Import database schema
   - Verify connection

4. **Test**
   - Access admin panel: `https://yourdomain.com/admin/login.php`
   - Test login with admin credentials
   - Verify all functionality works

5. **SSL Certificate**
   - Ensure HTTPS is enabled
   - Update all URLs to use HTTPS

## Post-Deployment

- [ ] Monitor error logs
- [ ] Test all functionality
- [ ] Verify SEO settings (sitemap.xml, robots.txt)
- [ ] Check performance (Lighthouse score)
- [ ] Verify legal pages are accessible (Impressum, Datenschutz, AGB)

## Troubleshooting

### Database Connection Issues
- Check database credentials
- Verify database server is accessible
- Check firewall rules

### File Upload Issues
- Verify `uploads/` folder has write permissions (755 or 775)
- Check PHP `upload_max_filesize` and `post_max_size` settings

### 2FA Not Working
- Check if `admin/2fa.txt` file is writable (for local development)
- For production, configure email settings in `admin/login.php`

### Path Issues
- Ensure all paths are relative (not absolute with `/`)
- Check `.htaccess` for proper rewrite rules

## Support

For issues or questions, contact the development team.

