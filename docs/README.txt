======
README
======
This directory should be used to place project specfic documentation including but not limited to 
project notes, generated API/phpdoc documentation, or manual files generated or hand written.  
Ideally, this directory would remain in your development environment only and should not be deployed 
with your application to it's final production location.


============
REQUIREMENTS
============
MySQL 5.1
PHP 5.3
WinLess (http://winless.org/) or a different LESS compiler for the less css language
Zend Framework 1.11


==================================
INCLUDE LIBRARIES (In the Project)
==================================
Zend Framework 1.x
PHPUnit


============
DEVELOPMENT
============
In order to development on this project you will need to point WinLess at the styles.less file and 
the bootstrap.less that are located in their respective less folders in the default theme.

You will also need to configure the application.ini with the proper values for yourself monitor 
for new changes in the application.ini.tmpl file.

Once this is all done and you have a database connection that works, you can right click on the 
load.mysql.php file and run it as a PHP CLI application. If you want to load data automatically you 
will need to modify the run configuration to run under PHP 5.3 CLI (NOT CGI) and then change the 
arguments to be -w.

Once this is done one you do not need to right click on the file again, it will be available under 
the run button (looks like a play button). Make sure to name your run configuration appropriately.


============
UNIT TESTING
============
Step 1 (Only needs to be done once): Right click on tests/phpunit.xml and run as a phpunit test. 
									 Make sure to change the name to something appropriate so 
								 	 that you can reuse it in the future. 

You can run the unit tests over and over by going to the run button and selecting the appropriately
named item. If you have the php unit tab you can open it and click the run again tab to rerun tests. 
This makes repeated tests much easier to do.


=====================
Setting Up Your VHOST
=====================
The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "D:/Sites/{websitename}/public"
   ServerName .local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development

   <Directory "D:/Sites/{websitename}/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>

======================================================================================================
======================================================================================================