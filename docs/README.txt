README
======
This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.

REQUIREMENTS
============
MySQL 5.1
PHP 5.3
WinLess (http://winless.org/) or a different LESS compiler for the less css language
Zend Framework 1.11

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