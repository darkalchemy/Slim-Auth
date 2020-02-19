# Slim-Auth
A Slim 4 Skeleton.

![GitHub commits since tagged version](https://img.shields.io/github/commits-since/darkalchemy/Slim-Auth/0.3.3)
[![GitHub Issues](https://img.shields.io/github/issues/darkalchemy/Slim-Auth)](https://github.com/darkalchemy/Slim-Auth/issues)
[![GitHub license](https://img.shields.io/github/license/darkalchemy/Slim-Auth.svg)](https://github.com/darkalchemy/Slim-Auth/blob/master/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/darkalchemy/Slim-Auth.svg)](https://packagist.org/packages/darlachemy/slim-auth)

This is a simple skeleton to quickly ramp up a slim 4 project.  

PHP 7.4 is required  
Composer is required  
npm is required  

[Slim 4](https://github.com/slimphp/Slim) as the core framework  
[nyholm/psr7](https://github.com/Nyholm/psr7) for the PSR-7 implementation  
[php-di/php-di](http://php-di.org/) to manage dependency injection    
[Cartalyst/Sentinel](https://cartalyst.com/manual/sentinel/3.) for user authentication and authorization  
[delight-im/PHP-I18N](https://github.com/delight-im/PHP-I18N) for generating the files needed for poedit  
[jobby](https://github.com/jobbyphp/jobby) to run all background jobs through cron  
[Eloquent/ORM](https://github.com/illuminate/database) for database storage  
[EmailValidator](https://github.com/egulias/EmailValidator/tree/master) for validating emails
[Middlewares/Trailing-slash](https://github.com/middlewares/trailing-slash) to remove any trailing slashes in the url  
[Monolog](https://github.com/Seldaek/monolog) for logging  
[PHPMailer](https://github.com/PHPMailer/PHPMailer) for sending email  
[Phinx](https://phinx.org/) for database migrations  
[Odan/Session](https://github.com/odan/session) for managing the session  
[Odan/Twig-Translation](https://github.com/odan/twig-translation) (partial usage) for compiling twig templates  
[Selective/Config](https://github.com/selective-php/config) to manage config settings  
[Slim/CSRF](https://github.com/slimphp/Slim-Csrf) to protect against csrf  
[Slim/Flash](https://github.com/slimphp/Slim-Flash) for flash messaging  
[Slim/Twig/Flash](https://github.com/kanellov/slim-twig-flash)(updated) for displaying flash messages in twig  
[Slim/Twig-View](https://github.com/slimphp/Twig-View) for templates  
[Vlucas/Valitron](https://github.com/vlucas/valitron) for validation  
[Slim/Whoops](https://github.com/zeuxisoo/php-slim-whoops) for displaying errors  

To install with composer:
```
composer create-project darkalchemy/slim-auth
```
edit config/settings.php as needed.

Make these folders writable by the web server
```
sudo chown -R www-data:www-data var/{cache,logs,tmp} resources/views/cache/
sudo chmod -R 0775 var/{cache,logs,tmp} resources/views/cache/
```

For Development:
```
npm install
composer migrate
npm run build-dev
```

For Production:
```
composer install --no-dev
npm install
composer migrate
npm run build
```

Set up cron job, this is necessary to be able to run scripts as www-data when needed:
```
sudo crontab -e

## add this to root crontab
* * * * * cd /path/to/bootstrap/ && /usr/bin/php jobby.php 1>> /dev/null 2>&1
```

Emails are not sent directly, they are inserted into the database and jobby will take care of sending them.

For simplicity, the locale is set automatically by the HTTP request header Accept-Language. This may change to using a query string to set the locale with a fallback to the HTTP request header.  

Compile twig templates for translating:
```
composer compile
sudo chown -R www-data:www-data resources/views/cache/
sudo chmod -R 0775 resources/views/cache/
```

Add additional locales:
```
## check if locale is installed
locale -a

## find correct local
nano /usr/share/i18n/SUPPORTED

## add locale if not already installed (fr_FR)
sudo locale-gen fr_FR
sudo locale-gen fr_FR.UTF-8
sudo update-locale

## restart webserver (apache2|nginx)
sudo service nginx restart

## edit bootstrap/container.php and add the correct local to the 'I18n::class' section
nano bootstrap/container.php
```

Translate all php files to locale - en_US and fr_FR:
```
composer translate en_US
composer translate fr_FR
```

Then open locale/[en_US|fr_FR]/LC_MESSAGES/messages.po in poedit and edit translation.  

### TODO    
phpunit for testing.

### Credits  
Much of what I have done here I learned from watching videos on youtube, [Laracasts](https://laracasts.com/), [Codecourse](https://codecourse.com) and from what I have read in many of the online tutorials and Slim Skeletons on github.  
[Slim4-Skeleton](https://github.com/odan/slim4-skeleton), [Slim-Skeleton](https://github.com/slimphp/Slim-Skeleton) and [Slim4-Starter](https://github.com/akrabat/slim4-starter) to list just a few.  

I still have a long way to go, but I'm enjoying the trip.  
