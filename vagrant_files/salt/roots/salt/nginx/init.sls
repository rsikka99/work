nginx:
  pkg.latest:
    - refresh: True
  service.running:
    - enable: True
    - watch:
      - file: nginx-vhost-default
      - file: nginx-vhost-default-enabled


nginx-vhost-default:
  file.managed:
    - name: /etc/nginx/nginx.conf
    - source: salt://nginx/packages/nginx/nginx.conf
    - require:
      - pkg: nginx

nginx-vhost-default:
  file.managed:
    - name: /etc/nginx/sites-available/default
    - source: salt://nginx/packages/nginx/sites-available/default.nginx
    - require:
      - pkg: nginx

nginx-vhost-default-enabled:
  file.symlink:
    - name: /etc/nginx/sites-enabled/default
    - target: /etc/nginx/sites-available/default
    - require:
      - pkg: nginx
