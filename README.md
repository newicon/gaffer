# gaffer
a minimal [PSR](https://www.php-fig.org/psr/)/[composer](https://getcomposer.org/) based framework demo

## requirements
- local webserver : apache2 (due to the current .htaccess requirement)
- PHP 8.1 +
- MySQL/Maria 15.1+
- interwebs! uses various CDN libraries
- SCSS if you wish to compile the css from sources

## structure
/app - demo app
/lib - any non-composer based libraries/dependencies
/public - the public html directory
/public/assets - fixed assets (css/js/images/etc)
/image - uploadable and/or resized images
/media - uploadable media

## installation
- clone this repo (duh!)
- setup hosts/vhosts (as required) pointing to /public
- create a /config.ini file like this, something like this
```
ENV = 'development'
DEBUG = 'true'
TIMEZONE = 'Europe/London'
EMAIL = 'gaffer@newicon.net'

DB_HOST = 'localhost'
DB_NAME = 'gaffer_dev'
DB_USER = 'root'
DB_PASS = 'root'
DB_CHAR = 'utf8'
```
- create a local database using the scripts in /sql 
- browse away!
- to view the admin area you will need to create an admin user via cli.php
```php cli.php add-user {email} {password}```

## notes
- highly PSR compliant
- composer