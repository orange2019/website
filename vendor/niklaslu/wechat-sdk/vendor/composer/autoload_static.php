<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit732d5a29349543bdd9dd457d242c9114
{
    public static $prefixLengthsPsr4 = array (
        'n' => 
        array (
            'niklaslu\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'niklaslu\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit732d5a29349543bdd9dd457d242c9114::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit732d5a29349543bdd9dd457d242c9114::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
