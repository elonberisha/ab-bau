# Deployment Checklist - AB Bau Fliesen

## ✅ Completed Pre-Deployment Tasks

- [x] Removed temporary files (`admin/about copy.php`, `admin/last_otp.txt`)
- [x] Removed documentation files (PERFORMANCE_*.md, SEO_*.md, LEGAL_*.md)
- [x] Updated `admin/includes/db_connect.php` with environment variable support
- [x] Fixed all absolute paths to relative paths in admin panel
- [x] Fixed favicon paths in legal pages (agb.php, datenschutz.php, impressum.php)
- [x] Created `.gitignore` file
- [x] Created `DEPLOYMENT.md` guide
- [x] Created `README.md` file

## 🔧 Required Configuration Before Deployment

### 1. Database Configuration
**File:** `admin/includes/db_connect.php`

Update with production database credentials:
```php
$db_host = 'your_production_host';
$db_user = 'your_production_user';
$db_pass = 'your_production_password';
$db_name = 'your_production_database';
```

Or set environment variables on your server:
- `DB_HOST`
- `DB_USER`
- `DB_PASS`
- `DB_NAME`

### 2. File Permissions
Set proper permissions:
```bash
chmod 755 uploads/
chmod 644 admin/includes/db_connect.php
chmod 644 .htaccess
```

### 3. Database Setup
- [ ] Create production database
- [ ] Import database schema
- [ ] Create admin user (or migrate existing)
- [ ] Test database connection

### 4. Server Configuration
- [ ] PHP 7.4+ installed
- [ ] MySQL 5.7+ installed
- [ ] Apache mod_rewrite enabled
- [ ] GD Library enabled
- [ ] SSL certificate installed (HTTPS)

### 5. Security
- [ ] Change default admin password
- [ ] Verify `.htaccess` is working
- [ ] Check file permissions
- [ ] Disable directory listing
- [ ] Enable error logging (disable display_errors in production)

### 6. Testing
- [ ] Test admin panel login
- [ ] Test user creation/editing/deletion
- [ ] Test image uploads
- [ ] Test all CRUD operations
- [ ] Test 2FA functionality
- [ ] Test frontend pages
- [ ] Test API endpoints
- [ ] Test legal pages (AGB, Impressum, Datenschutz)
- [ ] Test contact form
- [ ] Test review submission

### 7. SEO & Performance
- [ ] Verify sitemap.xml is accessible
- [ ] Verify robots.txt is accessible
- [ ] Check all meta tags
- [ ] Test Lighthouse score
- [ ] Verify all images load correctly
- [ ] Check mobile responsiveness

### 8. Content
- [ ] Review all content for accuracy
- [ ] Check all links work
- [ ] Verify contact information
- [ ] Check legal pages content
- [ ] Review SEO meta descriptions

## 📋 Files to Upload

### Required Files
- All `.html` files (root)
- All `.php` files (root and admin/)
- `admin/` folder (entire directory)
- `api/` folder
- `assets/` folder
- `css/` folder
- `js/` folder
- `uploads/` folder (with proper permissions)
- `.htaccess` file
- `robots.txt`
- `sitemap.xml`
- `site.webmanifest`
- All favicon files
- `logo.svg`

### Optional Files (can be excluded)
- `node_modules/` (not needed in production)
- `package.json` and `package-lock.json` (if not using build process)
- `css/input.css` (source file, not needed if using compiled CSS)
- Documentation files (`.md` files, except README.md)

## 🚫 Files NOT to Upload

- `admin/about copy.php` (already deleted)
- `admin/last_otp.txt` (already deleted)
- `node_modules/` (excluded in .gitignore)
- `.git/` folder (if using Git)
- `.env` files (if using environment variables)
- Development documentation files (already deleted)

## 🔍 Post-Deployment Verification

1. **Access Website**
   - [ ] Homepage loads correctly
   - [ ] All pages accessible
   - [ ] No 404 errors

2. **Admin Panel**
   - [ ] Login works
   - [ ] Dashboard loads
   - [ ] All admin functions work

3. **Functionality**
   - [ ] Image uploads work
   - [ ] Content updates work
   - [ ] Forms submit correctly
   - [ ] API endpoints respond

4. **Performance**
   - [ ] Page load times acceptable
   - [ ] Images optimized
   - [ ] No console errors

5. **Security**
   - [ ] HTTPS enabled
   - [ ] No sensitive data exposed
   - [ ] File permissions correct

## 📞 Support

If you encounter any issues during deployment, refer to `DEPLOYMENT.md` for detailed troubleshooting steps.

---

**Last Updated:** 2024

