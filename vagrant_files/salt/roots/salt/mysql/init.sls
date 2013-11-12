include:
  - salt.modules.mysql

mysql-server-5.5:
  pkg.latest:
    - refresh: True

mysql-client-5.5:
  pkg.latest:
    - refresh: True

python-mysqldb:
  pkg.latest:
      - refresh: True

mysql-service:
  service.running:
    - name: mysql
    - restart: True
    - require:
      - pkg: mysql-server-5.5
      - pkg: python-mysqldb

mpstoolbox:
  mysql_user.present:
    - host: "localhost"
    - password: "tmtwdev"
    - require:
      - service: mysql
  mysql_database:
    - present
    - require:
      - service: mysql
  mysql_grants.present:
    - grant: all privileges
    - database: "mpstoolbox.*"
    - user: "mpstoolbox"
    - host: "localhost"
    - require:
      - service: mysql