# gaffer
Minimal [PSR](https://www.php-fig.org/psr/)/[composer](https://getcomposer.org/) based framework.
Utilises defined PSR standards where possible and aims to keep dependencies to a minimum.

## requirements
- PHP 8.3 +
- MySQL/Maria 15.1+
- interwebs! uses various CDN libraries
- SCSS if you wish to compile the css from sources
- local webserver : apache2 (due to the current .htaccess requirement) or even just PHP's build in webserver

## installation
- clone this repo
- in the root dir run ```composer install```
- navigate to /example and run  ```composer install```
- setup hosts/vhosts (as required) pointing to /example/public
- copy config.ini.sample to /example/config.ini and update as necessary
- create a local database using the scripts in /example/sql
- mount /example/public browse away using your local webserver of choice (see below)
- to view the admin area you will need to create an admin user via cli.php
```php cli.php add-user {email} {password}```
  
## Notes
- highly [PSR](https://www.php-fig.org/psr/) compliant
- highly [composer](https://getcomposer.org/) based ... uses the composer autoloader with additional PSR4 support
- the example has auto/magic image resizing ... place an image in /image then use {image_name}_{width}_{height}.{extension} for intervention to resize it!
- as mentioned uses various CDN distributed libraries

## Todo
- dedicated CLI bootstrap
- add a [PSR-15](https://www.php-fig.org/psr/psr-15/) based middleware support with a couple of example implementations
- further unit/acceptance tests
- rename /image to /images!!

## Codeception Tests
Run the unit & acceptance tests like this:
```
./codecept run
```
Additionally generate a coverage report (to /_output/coverage) like this:
```
./codecept run --coverage --coverage-html
```

## Local Webserver
You can view the example using PHP's built in webserver, something like this:
```
php -S localhost:8000 example/public/router.php
```
If you have valet installed then on the command line go to gaffer/example/public and add a valet link
```
valet link gaffer
```
If using apache then just mount gaffer/example/public as a VirtualHost and it wil use the provided .htaccess
If using nginx then you'll need both mount gaffer/example/public and rewrite traffic to index.php using try_files

## Troubleshooting
During coverage report generation you may see this error:
```XDEBUG_MODE=coverage or xdebug.mode=coverage has to be set```
If so then you can either run it like this
```XDEBUG_MODE=coverage && ./codecept run --coverage --coverage-html```
or update your php.ini settings for xdebug with multiple available modes like this
```xdebug.mode=develop,debug,coverage```
... and then restart php-fpm

## Security
Any security issues spotted please email developers@newicon.net
