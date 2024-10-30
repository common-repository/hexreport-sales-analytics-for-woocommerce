<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5dbff96e809a47cdf7473f9f1f520936
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Kathamo\\Framework\\' => 18,
        ),
        'H' => 
        array (
            'HexReport\\Database\\' => 19,
            'HexReport\\App\\' => 14,
        ),
        'C' => 
        array (
            'Codesvault\\Validator\\' => 21,
            'CodesVault\\Howdyqb\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Kathamo\\Framework\\' => 
        array (
            0 => __DIR__ . '/..' . '/codesvault/kathamo-framework/src',
        ),
        'HexReport\\Database\\' => 
        array (
            0 => __DIR__ . '/../..' . '/database',
        ),
        'HexReport\\App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
        'Codesvault\\Validator\\' => 
        array (
            0 => __DIR__ . '/..' . '/codesvault/validator/src',
        ),
        'CodesVault\\Howdyqb\\' => 
        array (
            0 => __DIR__ . '/..' . '/codesvault/howdy-qb/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5dbff96e809a47cdf7473f9f1f520936::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5dbff96e809a47cdf7473f9f1f520936::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5dbff96e809a47cdf7473f9f1f520936::$classMap;

        }, null, ClassLoader::class);
    }
}
