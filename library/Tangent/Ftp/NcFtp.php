<?php
namespace Tangent\Ftp;

class NcFtp
{
    public function get($url, $user, $pass, $dest) {
        $parts = parse_url($url);
        return $this->ext_get($parts['host'], $parts['path'], $user, $pass, $dest);
    }

    public function ext_get($host, $path, $user, $pass, $dest) {
        $pass = str_replace('$','\$',$pass);
        $cmd = "ncftpget -C -u {$user} -p {$pass} {$host} {$path} {$dest}";
        echo "$cmd\n";
        $result = shell_exec($cmd);
        return $result;
    }
}
