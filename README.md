# AB Bau Fliesen - Website

Professional construction and tile installation company website for Petershausen, Germany.

## 🌐 Live Website
**URL:** https://ab-bau-fliesen.de/

## 📋 Features

- **Responsive Design** - Mobile-first approach, optimized for all devices
- **SEO Optimized** - Meta tags, structured data, sitemap, robots.txt
- **Legal Compliance** - Impressum, Datenschutzerklärung, AGB pages
- **Admin Panel** - Full content management system
- **Performance Optimized** - Fast loading times, optimized assets
- **German Language** - Fully localized for German audience

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3 (Tailwind CSS), JavaScript
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Server:** Apache with mod_rewrite

## 📁 Project Structure

```
ab-bau/
├── admin/              # Admin panel
│   ├── includes/       # Shared includes (sidebar, db_connect)
│   └── *.php          # Admin pages
├── api/                # API endpoints
├── assets/             # Static assets (fonts, icons)
├── css/                # Stylesheets
├── js/                 # JavaScript files
├── uploads/            # User uploaded files
├── *.html              # Public pages
└── *.php               # Legal pages (AGB, Impressum, Datenschutz)
```

## 🚀 Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed deployment instructions.

### Quick Start

1. Upload all files to your server
2. Configure database in `admin/includes/db_connect.php`
3. Set proper permissions on `uploads/` folder (755)
4. Import database schema
5. Access admin panel: `https://yourdomain.com/admin/login.php`

## 🔐 Admin Panel

- **URL:** `https://ab-bau-fliesen.de/admin/login.php`
- **Default Password:** Change after first login!

### Admin Features

- Dashboard with statistics
- Media library management
- Content management (Hero, About, Services, Projects, Catalogs)
- Review management
- User management
- Legal pages management
- Contact information management

## 📝 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB)
- Apache with mod_rewrite enabled
- GD Library for image processing
- Write permissions on `uploads/` folder

## 🔧 Configuration

### Database Configuration

Edit `admin/includes/db_connect.php`:

```php
$db_host = 'your_database_host';
$db_user = 'your_database_user';
$db_pass = 'your_database_password';
$db_name = 'your_database_name';
```

Or use environment variables (recommended for production).

## 📄 License

Proprietary - All rights reserved

## 📞 Support

For support or questions, contact the development team.

---

**Last Updated:** 2024

