nginx:
  pkg.latest:
    - name: nginx
  service:
    - running
    - watch:
      - file: nginx-vhost-default
      - file: nginx-vhost-default-code
      - file: nginx-conf
      - file: nginx-vhost-default-enabled

nginx-conf:
  file.managed:
    - name: /etc/nginx/nginx.conf
    - template: jinja
    - source: salt://nginx/packages/nginx/nginx.conf
    - require:
      - pkg: nginx
      - file: nginx-includes-normal-fastcgi
      - file: nginx-includes-wordpress-fastcgi
      - file: nginx-includes-default-server-config
      - file: nginx-includes-wordpress-server-config
      - file: nginx-includes-ssl-config

nginx-includes-normal-fastcgi:
  file.managed:
    - name: /etc/nginx/includes/normal-fastcgi.conf
    - makedirs: true
    - template: jinja
    - source: salt://nginx/packages/nginx/includes/normal-fastcgi.conf
    - require:
      - pkg: nginx

nginx-includes-wordpress-fastcgi:
  file.managed:
    - name: /etc/nginx/includes/wordpress-fastcgi.conf
    - makedirs: true
    - template: jinja
    - source: salt://nginx/packages/nginx/includes/wordpress-fastcgi.conf
    - require:
      - pkg: nginx

nginx-includes-default-server-config:
  file.managed:
    - name: /etc/nginx/includes/default-server-config.conf
    - makedirs: true
    - template: jinja
    - source: salt://nginx/packages/nginx/includes/default-server-config.conf
    - require:
      - pkg: nginx

nginx-includes-wordpress-server-config:
  file.managed:
    - name: /etc/nginx/includes/wordpress-server-config.conf
    - makedirs: true
    - template: jinja
    - source: salt://nginx/packages/nginx/includes/wordpress-server-config.conf
    - require:
      - pkg: nginx

nginx-includes-ssl-config:
  file.managed:
    - name: /etc/nginx/includes/ssl-config.conf
    - makedirs: true
    - template: jinja
    - source: salt://nginx/packages/nginx/includes/ssl-config.conf
    - require:
      - pkg: nginx

nginx-vhost-default:
  file.managed:
    - name: /etc/nginx/sites-available/default
    - source: salt://nginx/packages/nginx/sites-available/default.nginx
    - require:
      - pkg: nginx
      - file: nginx-vhost-default-code

nginx-vhost-default-code:
  file.recurse:
    - name: /home/lrobert/apps/default
    - source: salt://nginx/packages/default-site

nginx-vhost-default-enabled:
  file.symlink:
    - name: /etc/nginx/sites-enabled/default
    - target: /etc/nginx/sites-available/default
    - require:
      - pkg: nginx

#
# Install SSL Certificates
#
{% for key, value in pillar.get('ssl_certificates', {}).items() %}
/usr/local/nginx/{{ value.get('certificate').get('name') }}:
  file.managed:
    - require:
      - file: nginx-conf
    - template: jinja
    - source: salt://nginx/packages/ssl/ssl.cert
    - context:
        key_name: {{ key }}

/usr/local/nginx/{{ value.get('private-key').get('name') }}:
  file.managed:
    - require:
      - file: nginx-conf
    - template: jinja
    - source: salt://nginx/packages/ssl/ssl.key
    - context:
        key_name: {{ key }}
{% endfor %}