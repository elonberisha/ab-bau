# SMTP Configuration Guide

## Setup Instructions

### For Gmail (Recommended for testing)

1. **Enable 2-Step Verification**
   - Go to your Google Account settings
   - Security → 2-Step Verification
   - Enable it

2. **Generate App Password**
   - Go to Google Account → Security
   - Under "2-Step Verification", click "App passwords"
   - Select "Mail" and "Other (Custom name)"
   - Enter "AB Bau Admin" as the name
   - Copy the generated 16-character password

3. **Update Configuration**
   - Open `admin/includes/email_config.php`
   - Update `SMTP_USERNAME` with your Gmail address
   - Update `SMTP_PASSWORD` with the App Password (not your regular password)

### Configuration File

Edit `admin/includes/email_config.php`:

```php
define('SMTP_ENABLED', true);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USERNAME', 'elonberisha1999@gmail.com'); // Your Gmail
define('SMTP_PASSWORD', 'your-16-char-app-password'); // App Password from Google
```

### For Other Email Providers

#### Outlook/Hotmail
```php
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
```

#### Yahoo
```php
define('SMTP_HOST', 'smtp.mail.yahoo.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
```

#### Custom SMTP Server
```php
define('SMTP_HOST', 'smtp.yourdomain.com');
define('SMTP_PORT', 587); // or 465 for SSL
define('SMTP_SECURE', 'tls'); // or 'ssl' for port 465
```

## Testing

After configuration, test by:
1. Trying to login to admin panel (2FA email should be sent)
2. Submitting contact form (email should be sent to anduena@ab-bau-fliesen.de)

## Troubleshooting

### Email not sending
- Check SMTP credentials are correct
- Verify App Password (for Gmail) is correct
- Check firewall allows outbound connections on SMTP port
- Check error logs in `admin/emails.txt` (if file fallback is enabled)

### Authentication failed
- For Gmail: Make sure you're using App Password, not regular password
- Check 2-Step Verification is enabled (for Gmail)
- Verify SMTP_USERNAME and SMTP_PASSWORD are correct

### Connection timeout
- Check SMTP_HOST and SMTP_PORT are correct
- Verify firewall settings
- Try different SMTP port (587 for TLS, 465 for SSL)

## Fallback

If SMTP fails, emails will be written to `admin/emails.txt` for local development.
This allows you to see what emails would have been sent.

