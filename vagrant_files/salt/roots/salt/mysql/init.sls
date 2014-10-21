mysql-server-5.5:
  pkg.latest:
    - name: 'mysql-server-5.5'

python-mysqldb:
  pkg.latest:
      - name: 'python-mysqldb'

mysql-service:
  service.running:
    - name: mysql
    - restart: True
    - watch:
      - file: mysql-conf
    - require:
      - pkg: mysql-server-5.5
      - pkg: python-mysqldb

{% if 'mysql_admin' in pillar %}
mysql-admin-user-{{ pillar['mysql_admin'].get('username', 'tmtw_admin') }}:
  mysql_user.present:
    - host: "{{ salt['pillar.get']('mysql_admin:hostname', '%') }}"
    - name: "{{ salt['pillar.get']('mysql_admin:username', 'tmtw_admin') }}"
    - password: "{{ salt['pillar.get']('mysql_admin:password', 'tmtwdev') }}"
    - require:
      - service: mysql
  mysql_grants.present:
    - grant: all privileges
    - grant_option: true
    - database: "*.*"
    - host: "{{ salt['pillar.get']('mysql_admin:hostname', '%') }}"
    - user: "{{ salt['pillar.get']('mysql_admin:username', 'tmtw_admin') }}"
    - require:
      - service: mysql
{% endif %}

mysql-conf:
  file.managed:
    - name: /etc/mysql/my.cnf
    - template: jinja
    - source: salt://mysql/packages/mysql/my.cnf
    - require:
      - pkg: mysql-server-5.5