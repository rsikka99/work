There are a few steps to follow to get up and running with MPS Toolbox.

## System Requirements: ##

* To proceed you'll need composer available on your command line.
* MySQL 5.5
* PHP 5.4 or higher
* Apache 2.2+ or nginx
* Apache and PHP will need write access to the folders under ```data/```, ```public/cache```, and ```public/downloads```
* A MySQL user with a blank database already created
* The web server needs to be pointing to the ```public/``` folder as that is the entry point of the application.
* This application is best used on it's own dedicated domain rather than a folder within a domain. EG: ```my.example.com``` instead of ```my.example.com/example/app/```

## Installation ##
1. Copy ```application/configs/local.php.dist``` to ```application/configs/local.php``` and fill out the appropriate values.
2. Copy ```phinx.yml.dist``` to ```phinx.yml``` and fill out appropriate values.
3. Run ```composer install```
4. Run ```vendor/bin/phinx migrate``` to migrate the database the latest version. You will need to make sure to run this command when getting updates from the repository to run any new migrations.

## Logging In ##

The system comes with a default user ```root@tangentmtw.com```. The default password for this user is ```tmtwdev```. You should create yourself a standard user under the Tangent MTW company to use for testing.
