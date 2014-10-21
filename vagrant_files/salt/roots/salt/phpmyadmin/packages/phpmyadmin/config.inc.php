<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */

$cfg['blowfish_secret'] = 'louc47bv6gcs6sjwwn';

/*
 * Servers configuration
 */
$i = 0;

$serverHosts = array(
{% if salt['pillar.get']('phpmyadmin:servers') %}
    {% for name, args in salt['pillar.get']('phpmyadmin:servers').iteritems() %}
    '{{ args['ip'] }}' => '{{ name }}',
    {% endfor %}
{% else %}
    '127.0.0.1' => '127.0.0.1',
{% endif %}
);

/*
 * First server
 */
foreach ($serverHosts as $serverHost => $serverName)
{
    $i++;
    /* Authentication type */
    $cfg['Servers'][$i]['auth_type'] = 'cookie';
    /* Server parameters */
    $cfg['Servers'][$i]['host']         = $serverHost;
    $cfg['Servers'][$i]['verbose']         = $serverName;
    $cfg['Servers'][$i]['connect_type'] = 'tcp';
    $cfg['Servers'][$i]['compress']     = false;
    /* Select mysql if your server does not have mysqli */
    $cfg['Servers'][$i]['extension']       = 'mysqli';
    $cfg['Servers'][$i]['AllowNoPassword'] = false;
}

/*
 * End of servers configuration
 */

$cfg['SendErrorReports'] = 'never';
$cfg['LoginCookieValidity'] = 36000;

/*
 * Directories for saving/loading files from server
 */
$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';

/**
 * Defines whether a user should be displayed a "show all (records)"
 * button in browse mode or not.
 * default = false
 */
$cfg['ShowAll'] = true;

/**
 * Number of rows displayed when browsing a result set. If the result
 * set contains more rows, "Previous" and "Next".
 * default = 30
 */
$cfg['MaxRows'] = 50;

/**
 * disallow editing of binary fields
 * valid values are:
 *   false    allow editing
 *   'blob'   allow editing except for BLOB fields
 *   'noblob' disallow editing except for BLOB fields
 *   'all'    disallow editing
 * default = blob
 */
//$cfg['ProtectBinary'] = 'false';