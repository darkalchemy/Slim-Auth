# Slim-Auth
A Slim 4 Skeleton.

![GitHub commits since tagged version](https://img.shields.io/github/commits-since/darkalchemy/Slim-Auth/0.3.17)
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
[delight-im/PHP-I18N](https://github.com/delight-im/PHP-I18N) for handling the users locale      
[hellogerard/jobby](https://github.com/jobbyphp/jobby) to run all background jobs through cron  
[Eloquent/ORM](https://github.com/illuminate/database) for database storage  
[EmailValidator](https://github.com/egulias/EmailValidator/tree/master) for validating email addresses    
[Middlewares/Trailing-slash](https://github.com/middlewares/trailing-slash) to remove any trailing slashes in the url  
[Monolog](https://github.com/Seldaek/monolog) for logging  
[PHPMailer](https://github.com/PHPMailer/PHPMailer) for sending email  
[Phinx](https://phinx.org/) for database migrations  
[Odan/Session](https://github.com/odan/session) for managing the session    
[Selective/Config](https://github.com/selective-php/config) to manage config settings  
[Slim/CSRF](https://github.com/slimphp/Slim-Csrf) to protect against csrf  
[Slim/Flash](https://github.com/slimphp/Slim-Flash) for flash messaging  
[Slim/Twig/Flash](https://github.com/kanellov/slim-twig-flash) (updated and included manually) for displaying flash messages in twig  
[Slim/Twig-View](https://github.com/slimphp/Twig-View) for templates  
[Slim/Whoops](https://github.com/zeuxisoo/php-slim-whoops) for displaying errors  
[Twig-Translate](https://github.com/darkalchemy/Twig-Translate) for translations  
[umpirsky/composer-permissions-handler](https://github.com/umpirsky/PermissionsHandler) to set folder permissions for log and cache folders  
[Vlucas/Valitron](https://github.com/vlucas/valitron) for validation  
[uma/redis-session-handler](https://github.com/1ma/RedisSessionHandler) for session handler if using redis for session handling  

To install with composer:
```
composer create-project darkalchemy/slim-auth
```

cd into project, edit config/settings.php as needed and create the database.
```
cd slim-auth
nano config/settings.php
```

For Development:
```
npm install        # install dependencies
npm run build-dev  # create initial js/css resources
composer compile   # compile twig templates
composer migrate   # import database
```

For Production:
```
composer install --no-dev  # install non-dev dependencies
npm install                # install dependencies
npm run build              # create initial js/css resources
composer compile           # compile twig templates
composer migrate           # import database
```

Set up cron job, this is necessary to be able to run scripts as www-data when needed:
```
sudo crontab -e

## add this to root crontab
* * * * * cd /path/to/bootstrap/ && /usr/bin/php jobby.php 1>> /dev/null 2>&1
```

Emails do not get sent directly, they are inserted into the database and jobby will take care of sending them.

Compile twig templates for translating:
```
composer compile
```

Translate all php files to locale - en_US:
```
composer translate en_US
```

Add additional locales:
```
## check if locale is installed
locale -a

## find correct local
nano /usr/share/i18n/SUPPORTED

## in order to test the locale switcher, I needed to have another locale translated. I translated 
## this using Google Translate, so the translation quality may not be very good. Please consider a 
## pull request to improve the quality of the translation.
## add locale if not already installed (fr_FR)
sudo locale-gen fr_FR
sudo locale-gen fr_FR.UTF-8
sudo update-locale

## restart webserver (apache2|nginx)
sudo service nginx restart

## edit bootstrap/container.php and add the correct locale to the 'I18n::class' section
nano bootstrap/container.php
```

Translate all php files to locale - fr_FR:
```
composer translate fr_FR
```
Then open locale/[en_US|fr_FR]/LC_MESSAGES/messages.po in poedit and edit translation.  

Then to create the binary forms of the translations, you need to run again for each locale:
```
composer translate en_EN
composer translate fr_FR
```

### Notes
If you want to use redis as your session handler, you should add this to php.ini and uncomment as needed, TCP or Socket:
```
; TCP
; session.save_handler = redis
; session.save_path    = "tcp://127.0.0.1:6379?database=1"

; UNIX Socket
; session.save_handler = redis
; session.save_path = "unix:///dev/shm/redis.sock?database=1"
```

### Available command line commands
```
composer cleanup          # runs php_cs_fixer 
composer clear_cache      # clears all file based caches
composer compile          # compile all twig templates
composer create-migration # create new migration class
composer migrate          # migrate the database
composer rollback         # rollback all database changes
composer set-perms        # set writable perms for cache/log folders for both webserver and cli 
composer translate [lang] # translate all strings for listed language
composer translate-all    # translate all strings for all currently available languages
npm build                 # create minified js/css resources
npm build-dev             # create js/css resources
```

### TODO    
phpunit for testing.

### Credits  
Much of what I have done here I learned from watching videos on youtube, [Laracasts](https://laracasts.com/), [Codecourse](https://codecourse.com) and from what I have read in many of the online tutorials and Slim Skeletons on github.  
[Slim4-Skeleton](https://github.com/odan/slim4-skeleton), [Slim-Skeleton](https://github.com/slimphp/Slim-Skeleton) and [Slim4-Starter](https://github.com/akrabat/slim4-starter) to list just a few.  

I still have a long way to go, but I'm enjoying the trip.  
