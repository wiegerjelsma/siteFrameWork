## siteFrameWork

In house development. PHP MVC framework. Inheritance based approach. Used for CMS, Websites and Applications.

---

MVC width a inheritance based approach. Every module inherit from the base in the framework. Every child application inherits from it's mother application. This works for all the different module types in the framework. Like; Applications / Sub Applications / Bootstrappers / Controllers / Modules / Library classes / Templates / Cronjobs / Config

---


# Applications

The framework is nothing but the foundation of a project. The project should be build up in applications.
Every applications can have sub applications. It is best practice to start with an application called 'site' and then create sub applications called 'site.back' and 'site.front'. The front application will be the frontend, the back application will be the cms. 

The framework is inheritence based, so;
All the 'get' logic can be placed inside the 'site' application, while the write logic should be placed inside the 'site.back' application. The 'site.back' application makes use of the get logic from it's mother application 'site'.


# Command Line Interface

The framework comes with an install sript to create files for the following.

- Applications
- Sub Applications
- Bootstrappers
- Controllers
- Modules
- Library classes
- Templates
- Cronjobs
- Config

Basic Usage

```
php install.php -c application:create -p myApp
php install.php -c controller:create -p myController -a myApp
```

The command consist of; calling the install script, passing through the command (seperated by :) and the name param (a) if necessary.


# Config files

The system cascades all the config and generates one config file with one `$cfg` array. This gives us a major performance boost. Only one file has to be included for each request.


# Cronjobs

You only have to edit the cronjobs file on your Linux webserver once. Make sure the cron.php in every application is called every minute. Let is spit it's output to the Log directory and corresponding logfile.

Edit the cronjobs.conf file in the framework or applications to set the cronjobs for each application.
This can be done by adding the following rule to the cronjobs array;

```php
$cron['*']['*']['*']['*']['*']['*'][] = 'image::handle?type=square&width=80,300,272,544&suffix=admin,admin-l,1x,2x&prefix=';
```

As you can see, vars can be passed to the method which is called at the given time.

The array is structured this way.

```php
$cron['*']['*']['*']['*']['*']['*'] = '*';
       |	|    |    |    |    |	   +--- Filename to call
       |    |    |    |    |    +---------- Server 				(DEV / STAGING / LIVE)
  	   |    |    |    |    +--------------- day of week 		(0 - 6) (Sunday=0)
	   |    |    |    +-------------------- month 				(1 - 12)
	   |    |    +------------------------- day of month 		(1 - 31)
	   |    +------------------------------ hour 				(0 - 23)
	   +----------------------------------- min 				(0 - 59)
```
 
