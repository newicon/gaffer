# gaffer
minimal/experimental [PSR](https://www.php-fig.org/psr/)/[composer](https://getcomposer.org/) based framework demo

__IMPORTANT NOTE :__    
__batteries not included ... this is very much a work in progress ... don't say you weren't warned!__

## requirements
- local webserver : apache2 (due to the current .htaccess requirement)
- PHP 8.3 +
- MySQL/Maria 15.1+
- interwebs! uses various CDN libraries
- SCSS if you wish to compile the css from sources

## installation
- clone this repo (duh!)
- setup hosts/vhosts (as required) pointing to /public
- copy config.ini.sample to /config.ini and update as necessary
- create a local database using the scripts in /sql
- run ```composer install```
- browse away!
- to view the admin area you will need to create an admin user via cli.php
```php cli.php add-user {email} {password}```

  
## notes
- highly [PSR](https://www.php-fig.org/psr/) compliant
- highly [composer](https://getcomposer.org/) based ... uses the composer autoloader with additional PSR4 support
- auto-magic image resizing ... place an image in /image then use {image_name}\_{width}\_{height}.{extension} for intervention to resize it!
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
  - [dougallwinship/deform](https://github.com/DougallWinship/deform) : form building library
  these are in order to build the demo app, you may not require them all for what you are doing!

## codeception


## todo
- security security security!
- separate the demo from the core & put it into its own repo
- where possible find existing open-source composer based libraries to replace functionality in /system and /lib:
    - favour light-weight libraries
    - favour mature/proven libraries
    - favour libraries with minimal dependencies of their own
- change the System namespace to Gaffer
- dedicated CLI bootstrap
- add a [PSR-15](https://www.php-fig.org/psr/psr-15/) based middleware support with a couple of example implementations 
- move the Deform library to its own repo
- unit/acceptance tests
- rename /image to /images!!

# Codeception Tests
You can mount /_data/public/ in a local webserver to see exactly what is being tested by the acceptance tests.

Run the unit & acceptance tests like this:
```
./codecept run
```

Additionally generate a coverage report (to /_output/coverage) like this:
```
./codecept run --coverage --coverage-html
```

## PHP Webserver
You can view the acceptance test pages using PHP's built in webserver, something like this:
> ```php -S localhost:8000 ./tests/Support/Data/public/router.php```

## Troubleshooting
During coverage report generation you may see this error:
```XDEBUG_MODE=coverage or xdebug.mode=coverage has to be set```
If so then you can either run it like this
```XDEBUG_MODE=coverage && ./codecept run --coverage --coverage-html```
or update your php.ini settings for xdebug with multiple available modes like this
```xdebug.mode=develop,debug,coverage```
... and then restart php-fpm
