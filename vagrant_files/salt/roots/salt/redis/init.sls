redis-server:
  pkg.latest:
    - name: 'redis-server'
  service:
    - running
    - name: redis-server
    - enable: True
    - require:
      - pkg: redis-server