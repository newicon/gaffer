# gaffer
a minimal [PSR](https://www.php-fig.org/psr/)/[composer](https://getcomposer.org/) based framework demo

__IMPORTANT NOTE :__    
__batteries not included ... this is very much a work in progress ... don't say you weren't warned!__

## requirements
- local webserver : apache2 (due to the current .htaccess requirement)
- PHP 8.1 +
- MySQL/Maria 15.1+
- interwebs! uses various CDN libraries
- SCSS if you wish to compile the css from sources

## structure
/app - the demo app   
/lib - any non-composer based libraries/dependencies (currently just a form handling library called Deform)   
/public - the public html directory   
/public/assets - fixed assets css/js/images/etc (in the repo)   
/image - uploadable and/or resized images (not in the repo)
/media - media (not in the repo)
/vendor - composer deps + the project autoloader   

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
- run ```composer install```
- browse away!
- to view the admin area you will need to create an admin user via cli.php
```php cli.php add-user {email} {password}```
  
## notes
- highly [PSR](https://www.php-fig.org/psr/) compliant
- highly [composer](https://getcomposer.org/) based ... uses the composer autoloader with additional PSR4 support
- automagic image resizing ... place an image in /image then use {image_name}_{width}_{height}.{extension} for imagemagik to resize it!
- as mentioned uses various CDN distributed libraries
- current composer dependencies are as follows:
  - [composer](https://github.com/composer/composer) : itself!
  - [guzzlehttp/psr7](https://github.com/guzzle/psr7) : [PSR-7](https://www.php-fig.org/psr/psr-7/) message implementation. dependency of intervention/image
  - [intervention/image](https://github.com/Intervention/image) : image manipulation
  - [laminas/dictactoros](https://github.com/laminas/laminas-diactoros) : [PSR-7](https://www.php-fig.org/psr/psr-7/) message implementations
  - [laminas/httphandlerunner](https://github.com/laminas/laminas-httphandlerrunner) : [PSR-7](https://www.php-fig.org/psr/psr-7/) responses & [PSR-15](https://www.php-fig.org/psr/psr-15/) server request handlers 
  - [league/plates](https://github.com/thephpleague/plates) : native PHP template system
  - [league/route](https://github.com/thephpleague/route) : [PSR-1](https://www.php-fig.org/psr/psr-1/), [PSR-2](https://www.php-fig.org/psr/psr-2/), [PSR-4](https://www.php-fig.org/psr/psr-4/), [PSR-7](https://www.php-fig.org/psr/psr-7/), [PSR-11](https://www.php-fig.org/psr/psr-15/), [PSR-15](https://www.php-fig.org/psr/psr-15/) compliant router
  - [nikic/fast-route](https://github.com/nikic/FastRoute) : regular expression based router. dependency of league/route
  - [opis/closure](https://github.com/opis/closure) : serializable closures. dependency of league/route
  - [psr/*](https://github.com/php-fig) : PSR definitions
  - [rakit/validation](https://github.com/rakit/validation) : validation library
  - [ralouphie/getallheaders](https://github.com/ralouphie/getallheaders) : PHP getallheaders() polyfill 🤷. dependency of guzzlehttps/psr7


## todo
- security security security!
- separate the demo from the core & put it into its own repo
- where possible find existing open-source composer based libraries to replace functionality in /system and /lib:
    - favour light-weight libraries
    - favour mature/proven libraries
    - favour libraries with minimal dependencies of their own
- dedicated CLI bootstrap
- move the Deform library to its own repo
- unit/acceptance tests
- rename /image to /images!!
