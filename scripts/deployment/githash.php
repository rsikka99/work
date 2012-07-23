<?php
/*
 * Gets the git hash for the project and outputs it to the screen
 */

$gitHash = "";
$appPath = realpath(__DIR__ . "../../");
$cmdGitLastRev = '/usr/bin/git rev-list --max-count=1 master';
if (is_dir($appPath . "/.git"))
{
    chdir($appPath);
    $hash = shell_exec($cmdGitLastRev);
}

echo $gitHash;
return 0;
