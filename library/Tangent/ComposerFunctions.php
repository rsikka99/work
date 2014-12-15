<?php

namespace Tangent;

use Composer\Package\Package;
use Composer\Script\Event;
use Composer\Script\PackageEvent;

class ComposerFunctions
{

    /**
     * Removes require_once from php files within a specified path. Except Zend Auto Loader and Zend Application
     *
     * @param $path
     */
    static function removeRequireOnceFromZend ($path)
    {
        shell_exec("find {$path} -name '*.php' -not -path '*/Loader/Autoloader.php' -not -path '*/Application.php' -print0 | xargs -0 sed --regexp-extended --in-place 's/(require_once)/\\/\\/ \\1/g'");
    }

    /**
     * Figures out if the package is the one we want to process
     *
     * @param Event   $event
     * @param Package $package
     */
    static function handlePackage (Event $event, Package $package)
    {
        $io = $event->getIO();

        /**
         * Handle Zend Framework
         */
        if (stristr($package->getName(), "zendframework1") !== false)
        {
            $installPath = $event->getComposer()->getInstallationManager()->getInstallPath($package);

            $io->write("  - Removing require_once entries from Zend ", false);
            self::removeRequireOnceFromZend($installPath . "/library/Zend");
            $io->write(" ... <info>DONE</info>");
            $io->write("");
        }
        else if (stristr($package->getName(), "zf1-extras") !== false)
        {
            $installPath = $event->getComposer()->getInstallationManager()->getInstallPath($package);
            $io->write("  - Removing require_once entries from ZendX", false);
            self::removeRequireOnceFromZend($installPath . "/library/ZendX");
            $io->write(" ... <info>DONE</info>");
            $io->write("");
        }

    }

    /**
     * Runs after a package is installed
     *
     * @param PackageEvent $event
     */
    static function postPackageInstall (PackageEvent $event)
    {
        self::handlePackage($event, $event->getOperation()->getPackage());
    }

    /**
     * Runs after a package is updated
     *
     * @param PackageEvent $event
     */
    static function postPackageUpdate (PackageEvent $event)
    {
        self::handlePackage($event, $event->getOperation()->getTargetPackage());
    }
}