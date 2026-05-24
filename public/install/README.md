# System Installation Guide

## Installation Steps

1. **Access the installer**: Navigate to `yourdomain.com/install` in your web browser.

2. **System Requirements**: The installer will check if your server meets all requirements:
   - PHP 8.1 or higher
   - MySQL 5.7+ or MariaDB 10.3+
   - Required PHP extensions (PDO, mbstring, openssl, etc.)
   - Writable directories

3. **Database Configuration**: Enter your database credentials:
   - Database Host (usually `localhost`)
   - Database Port (default: `3306`)
   - Database Name (will be created if doesn't exist)
   - Database Username
   - Database Password

4. **Site Configuration**: Set up your application:
   - Application Name
   - Application URL
   - Administrator Email
   - Administrator Password (minimum 8 characters)

5. **Installation**: The system will:
   - Create database tables
   - Configure environment settings
   - Create admin account
   - Set up initial data

## Security Notice

⚠️ **IMPORTANT**: After successful installation, delete the `/public/install` directory immediately for security reasons.

```bash
# Remove installation directory
rm -rf public/install
```

## Post-Installation

After installation:

1. Delete the installation directory
2. Login with your admin credentials
3. Configure additional settings from the admin panel
4. Set up mail configuration if needed
5. Configure file permissions properly

## Troubleshooting

### Common Issues:

**Database Connection Failed**
- Verify database credentials
- Ensure MySQL/MariaDB service is running
- Check if user has CREATE DATABASE privileges

**Permission Errors**
- Set proper permissions: `chmod -R 755 storage bootstrap/cache`
- Ensure web server user owns the directories

**White Screen/500 Error**
- Check PHP error logs
- Verify all PHP extensions are installed
- Ensure `.htaccess` is properly configured

## Reinstallation

To reinstall the system:

1. Delete or rename the `.env` file
2. Access `yourdomain.com/install` again
3. Follow the installation steps

## Uninstallation

To uninstall the system:

1. Access `yourdomain.com/install/uninstall.php`
2. Enter the uninstall password (for remote access)
3. This will remove configuration files but preserve the database

## Support

For issues or questions, please refer to the documentation or contact support.

## System Requirements

### Minimum Requirements:
- PHP 8.1.0 or higher
- MySQL 5.7+ / MariaDB 10.3+
- 256MB RAM
- 100MB disk space

### Recommended:
- PHP 8.2+
- MySQL 8.0+ / MariaDB 10.5+
- 512MB+ RAM
- 500MB+ disk space

### Required PHP Extensions:
- PDO
- PDO_MySQL
- Mbstring
- OpenSSL
- JSON
- Fileinfo
- CURL
- GD or ImageMagick
- ZIP

## License

This installation script is part of the Tourism Academy system.