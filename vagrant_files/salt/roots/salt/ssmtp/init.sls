ssmtp:
  pkg.latest:
    - name: "ssmtp"

/etc/ssmtp/ssmtp.conf:
  file.managed:
    - source: salt://ssmtp/packages/ssmtp/ssmtp.conf
    - template: jinja
    - require:
      - pkg: ssmtp
