# Slim-Auth
A Slim 4 Skeleton.

This is a simple skeleton to quickly ramp up a slim 4 project.  

PHP 7.4 is required  
Composer is required  
npm is required  

[Slim 4](https://github.com/slimphp/Slim) as the core framework  
[nyholm/psr7](https://github.com/Nyholm/psr7) for the PSR-7 implementation  
[php-di/php-di](http://php-di.org/) to manage dependency injection    
[Cartalyst Sentinel](https://cartalyst.com/manual/sentinel/3.) for user authentication and authorization  
[jobby](https://github.com/jobbyphp/jobby) to run all background jobs through cron  
[Eloquent ORM](https://github.com/illuminate/database) for database storage  
[EmailValidator](https://github.com/egulias/EmailValidator/tree/master) for validating emails  
[Monolog](https://github.com/Seldaek/monolog) for logging  
[PHPMailer](https://github.com/PHPMailer/PHPMailer) for sending email  
[Phinx](https://phinx.org/) for database migrations  
[Odan Session](https://github.com/odan/session) for managing the session(start session in middleware is not working)  
[Selective Config](https://github.com/selective-php/config) to manage config settings  
[Slim/CSRF](https://github.com/slimphp/Slim-Csrf) to protect against csrf  
[Slim/Flash](https://github.com/slimphp/Slim-Flash) for flash messaging  
[Slim/Twig/Flash](https://github.com/kanellov/slim-twig-flash)(updated) for displaying flash messages in twig  
[Twig-View](https://github.com/slimphp/Twig-View) for templates  
[Vlucas/Valitron](https://github.com/vlucas/valitron) for validation  
[Slim Whoops](https://github.com/zeuxisoo/php-slim-whoops) for displaying errors  
  

To install with composer:
```
composer create-project darkalchemy/slim-auth
```
edit config/settings.php as needed.

Set ownership
```
sudo chown -R www-data:www-data var/{cache,logs} resources/views/cache/
```

Development:
```
composer install
npm install
composer migrate
npm run build-dev
```

Production:
```
composer install --no-dev
npm install
composer migrate
npm run build
```

Set up cron job, add this to root crontab:
```
* * * * * cd /path/to/bootstrap/ && /usr/bin/php jobby.php 1>> /dev/null 2>&1
```

Emails are not sent directly, they are inserted into the database and jobby will take care of sending them.
  
### TODO  
i18n translation for twig.  
phpunit for testing.

### Credits  
Much of what I have done here I learned from watching videos on youtube, [Laracasts](https://laracasts.com/) and [Codecourse](https://codecourse.com) and from what I have read in many of the online tutorials and Slim Skeletons on github.  
[Slim4-Skeleton](https://github.com/odan/slim4-skeleton), [Slim-Skeleton](https://github.com/slimphp/Slim-Skeleton) and [Slim4-Starter](https://github.com/akrabat/slim4-starter) to list just a few.  
I still have a long way to go, but I'm enjoying the trip.   
