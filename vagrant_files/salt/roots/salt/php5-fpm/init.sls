php5_ppa:
  pkgrepo.managed:
    - ppa: ondrej/php5

php5-fpm:
  pkg.latest:
    - refresh: True
    - require:
      - pkgrepo: php5_ppa
    - pkgs:
      - php5-cli
      - php5-curl
      - php5-fpm
      - php5-gd
      - php5-gearman
      - php5-imagick
      - php5-intl
      - php5-json
      - php5-mcrypt
      - php5-mysql
      - php5-mysqlnd
      - php5-readline
      - php5-redis
      - php5-sqlite
      - php5-tidy
      - php5-xmlrpc
      - php5-xsl
  service.running:
    - enable: True
    - watch:
      - file: /etc/php5/fpm/pool.d/www.conf
  file.managed:
    - name: /etc/php5/fpm/pool.d/www.conf
    - source: salt://php5-fpm/packages/php/www.conf
    - user: root
    - group: root
    - mode: '0640'
    - require:
      - pkg: php5-fpm