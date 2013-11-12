nginx:
  pkg.latest:
      - refresh: True
  service.running:
      - enable: True