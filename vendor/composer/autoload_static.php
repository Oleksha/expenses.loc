<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc778507ffbe2ee613fc409aa59ab1324
{
    public static $prefixLengthsPsr4 = array (
        'e' => 
        array (
            'expenses\\' => 9,
        ),
        'a' => 
        array (
            'app\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'expenses\\' => 
        array (
            0 => __DIR__ . '/..' . '/expenses/core',
        ),
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc778507ffbe2ee613fc409aa59ab1324::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc778507ffbe2ee613fc409aa59ab1324::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc778507ffbe2ee613fc409aa59ab1324::$classMap;

        }, null, ClassLoader::class);
    }
}