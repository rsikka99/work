<?php
/*
 * Gets the git hash for the project and outputs it to the screen
 */

$gitHash = "";
$appPath = realpath(__DIR__ . "/../../");

$cmd = '/usr/bin/git';
if (stristr(PHP_OS, 'WIN'))
{
    // Git is in a different place when we're using windows
    $cmd = '"C:\Program Files (x86)\Git\bin\git.exe"';
}

$params = 'rev-list --max-count=1 HEAD';

if (is_dir($appPath . "/.git"))
{
    chdir($appPath);
    exec("{$cmd} {$params}", $output, $returnCode);
    if ($returnCode === 0)
    {
        $gitHash = implode(PHP_EOL, $output);
    }
}
echo $gitHash;
return 0;