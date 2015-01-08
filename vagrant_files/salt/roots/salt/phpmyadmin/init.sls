include:
  - nginx
  - php5-fpm

phpmyadmin:
  require:
    - sls: nginx
    - sls: php5-fpm
    - file: phpmyadmin-nginx-conf
    - file: phpmyadmin-conf
    - file: phpmyadmin-nginx-symlink
    - archive: phpmyadmin-files
    - file: phpmyadmin-permissions

nginx-phpmyadmin:
  require:
      - sls: nginx
      - sls: php5-fpm
  service.running:
      - name: nginx
      - enable: True
      - watch:
        - file: phpmyadmin-nginx-conf
        - file: phpmyadmin-nginx-symlink
        - archive: phpmyadmin-files

phpmyadmin-htpasswd:
  file.managed:
    - name: /etc/nginx/phpmyadmin.htpasswd
    - source: salt://phpmyadmin/packages/nginx/phpmyadmin.htpasswd
    - require:
      - sls: nginx

phpmyadmin-nginx-conf:
  file.managed:
    - name: /etc/nginx/sites-available/phpmyadmin
    - template: jinja
    - source: salt://phpmyadmin/packages/nginx/sites-available/phpmyadmin.conf
    - require:
      - sls: nginx
      - file: phpmyadmin-htpasswd

phpmyadmin-nginx-symlink:
  file.symlink:
    - name: /etc/nginx/sites-enabled/phpmyadmin
    - target: /etc/nginx/sites-available/phpmyadmin
    - require:
        - sls: nginx

phpmyadmin-directory:
  file.directory:
    - name: "/home/{{ salt['pillar.get']('user:username', 'lrobert') }}/apps/phpmyadmin"
    - user: "{{ salt['pillar.get']('user:username', 'lrobert') }}"
    - group: "{{ salt['pillar.get']('user:groupname', 'lrobert') }}"
    - makedirs: true

phpmyadmin-files:
  archive:
    - extracted
    - archive_format: zip
    - name: "/home/{{ salt['pillar.get']('user:username', 'lrobert') }}/apps/phpmyadmin"
    - source: "salt://phpmyadmin/packages/phpMyAdmin-4.3.6-english.zip"
    - if_missing: "/home/{{ salt['pillar.get']('user:username', 'lrobert') }}/apps/phpmyadmin/index.php"
    - require:
      - file: phpmyadmin-directory

phpmyadmin-conf:
  file.managed:
    - name: "/home/{{ salt['pillar.get']('user:username', 'lrobert') }}/apps/phpmyadmin/config.inc.php"
    - template: jinja
    - source: salt://phpmyadmin/packages/phpmyadmin/config.inc.php
    - require:
      - archive: phpmyadmin-files
      - file: phpmyadmin-directory

phpmyadmin-permissions:
  file.directory:
    - name: "/home/{{ salt['pillar.get']('user:username', 'lrobert') }}/apps/phpmyadmin"
    - user: "{{ salt['pillar.get']('user:username', 'lrobert') }}"
    - group: "{{ salt['pillar.get']('user:groupname', 'lrobert') }}"
    - require:
      - archive: phpmyadmin-files
    - recurse:
      - group
      - user