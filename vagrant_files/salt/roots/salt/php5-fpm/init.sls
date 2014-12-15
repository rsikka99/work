php5-fpm:
  pkg.latest:
    - pkgs:
      - php5-fpm
  service.running:
    - name: php5-fpm
    - enable: True
    - watch:
      - file: php5-fpm-pool
      - file: php5-fpm-ini
      - pkg: libedit2
      - pkg: php5-curl
      - pkg: php5-gd
      - pkg: php5-gearman
      - pkg: php5-imagick
      - pkg: php5-intl
      - pkg: php5-json
      - pkg: php5-mcrypt
      - file: /etc/php5/fpm/conf.d/20-mcrypt.ini
#      - file: /etc/php5/cli/conf.d/20-mcrypt.ini
      - pkg: php5-mysqlnd
      - pkg: php5-readline
      - pkg: php5-redis
      - pkg: php5-sqlite
      - pkg: php5-tidy
      - pkg: php5-xmlrpc
      - pkg: php5-xsl

php5-fpm-pool:
  file.managed:
    - name: /etc/php5/fpm/pool.d/www.conf
    - template: jinja
    - source: salt://php5-fpm/packages/php/www.conf
    - user: root
    - group: root
    - mode: '0640'
    - require:
      - pkg: php5-fpm

php5-fpm-ini:
  file.managed:
    - name: /etc/php5/fpm/php.ini
    - template: jinja
    - source: salt://php5-fpm/packages/php/fpm/php.ini
    - user: root
    - group: root
    - mode: '0644'
    - require:
      - pkg: php5-fpm

php5-cli-ini:
  file.managed:
    - name: /etc/php5/cli/php.ini
    - template: jinja
    - source: salt://php5-fpm/packages/php/cli/php.ini
    - user: root
    - group: root
    - mode: '0644'
    - require:
      - pkg: php5-cli


libedit2:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-cli:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-curl:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-gd:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-gearman:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-imagick:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-intl:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-json:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-mcrypt:
  pkg.latest:
    - require:
      - pkg: php5-fpm

/etc/php5/fpm/conf.d/20-mcrypt.ini:
  file.symlink:
    - target: /etc/php5/mods-available/mcrypt.ini
    - require:
        - pkg: php5-mcrypt

# Disabled because the cli version of 20-mcrypt can't be found at the moment.
#/etc/php5/cli/conf.d/20-mcrypt.ini:
#  file.symlink:
#    - target: /etc/php5/mods-available/mcrypt.ini
#    - require:
#        - pkg: php5-mcrypt

php5-mysqlnd:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-readline:
  pkg.latest:
    - require:
      - pkg: libedit2

php5-redis:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-sqlite:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-tidy:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-xmlrpc:
  pkg.latest:
    - require:
      - pkg: php5-fpm

php5-xsl:
  pkg.latest:
    - require:
      - pkg: php5-fpm