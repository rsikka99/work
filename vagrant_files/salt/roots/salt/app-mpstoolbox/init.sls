include:
  - nginx
  - php5-fpm
  - redis

tmtw-mpstoolbox:
  require:
    - sls: nginx
    - sls: php5-fpm
    - sls: redis
    - file: mpstoolbox-nginx-conf
    - file: mpstoolbox-nginx-symlink
  service.running:
      - name: nginx
      - enable: true
      - watch:
        - file: mpstoolbox-nginx-conf

mpstoolbox-nginx-conf:
  file.managed:
    - name: /etc/nginx/sites-available/mpstoolbox.conf
    - source: salt://app-mpstoolbox/packages/nginx/sites-available/mpstoolbox.conf
    - require:
      - sls: nginx

mpstoolbox-nginx-symlink:
  file.symlink:
    - name: /etc/nginx/sites-enabled/mpstoolbox.conf
    - target: /etc/nginx/sites-available/mpstoolbox.conf
    - require:
        - sls: nginx