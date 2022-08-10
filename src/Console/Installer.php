<?php

namespace OAuthServer\Console;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Exception;

/**
 * Composer installer object
 */
class Installer
{
    /**
     * Does some routine installation tasks so people don't have to.
     * Run after running a composer install, useful for initializing the package
     * with permissions and etcetera
     *
     * @param Event $event The composer event object.
     * @return void
     * @throws Exception Exception raised by validator.
     */
    public static function postInstall(Event $event)
    {
        $io = $event->getIO();
        define('DS', DIRECTORY_SEPARATOR);
        $rootDir = dirname(dirname(__DIR__)) . DS;
        static::setExampleKeyPermissionsForTest($rootDir, $io);
    }

    /**
     * Set globally writable permissions on the "tmp" and "logs" directory.
     *
     * This is not the most secure default, but it gets people up and running quickly.
     *
     * @param string      $path  File/folder path
     * @param string      $perms In bits e.g. '1100000000' (rw- --- ---)
     * @param IOInterface $io    IO interface to write to console.
     * @return void
     */
    protected static function setPathPermissions(string $path, string $perms, IOInterface $io): void
    {
        $perms        = bindec($perms);
        $currentPerms = fileperms($path) & 0777;
        if ($currentPerms == $perms) {
            return;
        }
        $res = chmod($path, $perms);
        if ($res) {
            $io->write('Permissions set on ' . $path);
        } else {
            $io->write('Failed to set permissions on ' . $path);
        }
    }

    /**
     * Set example key file permissions
     *
     * @param string      $rootDir
     * @param IOInterface $io
     * @return void
     */
    public static function setExampleKeyPermissionsForTest(string $rootDir, IOInterface $io): void
    {
        static::setPathPermissions($rootDir . 'config' . DS . 'private.example.key', '110000000', $io);
        static::setPathPermissions($rootDir . 'config' . DS . 'public.example.key', '110000000', $io);
    }
}