<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitc110741a69210b334084b2988fa26ff4
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Onepix\FoodSpotVendor\Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Onepix\FoodSpotVendor\Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitc110741a69210b334084b2988fa26ff4', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Onepix\FoodSpotVendor\Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitc110741a69210b334084b2988fa26ff4', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Onepix\FoodSpotVendor\Composer\Autoload\ComposerStaticInitc110741a69210b334084b2988fa26ff4::getInitializer($loader));

        $loader->setClassMapAuthoritative(true);
        $loader->register(true);

        $filesToLoad = \Onepix\FoodSpotVendor\Composer\Autoload\ComposerStaticInitc110741a69210b334084b2988fa26ff4::$files;
        $requireFile = \Closure::bind(static function ($fileIdentifier, $file) {
            if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
                $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;

                require $file;
            }
        }, null, null);
        foreach ($filesToLoad as $fileIdentifier => $file) {
            $requireFile($fileIdentifier, $file);
        }

        return $loader;
    }
}
