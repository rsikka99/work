<?php
namespace Tangent\Ftp;

class NcFtp
{
    public function get($url, $user, $pass, $dest) {
        $parts = parse_url($url);
        $cmd = "ncftpget -C -u {$user} -p {$pass} {$parts['host']} {$parts['path']} {$dest}";
        $result = shell_exec($cmd);
        return $result;
    }
}
