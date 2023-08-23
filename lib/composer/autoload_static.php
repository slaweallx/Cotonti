<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb62a2eb53da07861f5b198adce5bd706
{
    public static $prefixLengthsPsr4 = array (
        'c' => 
        array (
            'cot\\plugins\\' => 12,
            'cot\\modules\\' => 12,
            'cot\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'cot\\plugins\\' => 
        array (
            0 => __DIR__ . '/../..' . '/plugins',
        ),
        'cot\\modules\\' => 
        array (
            0 => __DIR__ . '/../..' . '/modules',
        ),
        'cot\\' => 
        array (
            0 => __DIR__ . '/../..' . '/system',
        ),
    );

    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/../..' . '/lib',
    );

    public static $classMap = array (
        'APC_driver' => __DIR__ . '/../..' . '/system/cache.php',
        'Cache' => __DIR__ . '/../..' . '/system/cache.php',
        'Cache_driver' => __DIR__ . '/../..' . '/system/cache.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Cot' => __DIR__ . '/../..' . '/system/Cot.php',
        'CotDB' => __DIR__ . '/../..' . '/system/database.php',
        'Cotpl_block' => __DIR__ . '/../..' . '/system/cotemplate.php',
        'Cotpl_data' => __DIR__ . '/../..' . '/system/cotemplate.php',
        'Cotpl_expr' => __DIR__ . '/../..' . '/system/cotemplate.php',
        'Cotpl_logical' => __DIR__ . '/../..' . '/system/cotemplate.php',
        'Cotpl_loop' => __DIR__ . '/../..' . '/system/cotemplate.php',
        'Cotpl_var' => __DIR__ . '/../..' . '/system/cotemplate.php',
        'Db_cache_driver' => __DIR__ . '/../..' . '/system/cache.php',
        'Dynamic_cache_driver' => __DIR__ . '/../..' . '/system/cache.php',
        'File_cache' => __DIR__ . '/../..' . '/system/cache.php',
        'JSMin' => __DIR__ . '/..' . '/jsmin.php',
        'JSMinException' => __DIR__ . '/..' . '/jsmin.php',
        'Memcache_driver' => __DIR__ . '/../..' . '/system/cache.php',
        'MySQL_cache' => __DIR__ . '/../..' . '/system/cache.php',
        'Page_cache' => __DIR__ . '/../..' . '/system/cache.php',
        'Resources' => __DIR__ . '/../..' . '/system/Resources.php',
        'Static_cache_driver' => __DIR__ . '/../..' . '/system/cache.php',
        'Temporary_cache_driver' => __DIR__ . '/../..' . '/system/cache.php',
        'Writeback_cache_driver' => __DIR__ . '/../..' . '/system/cache.php',
        'XTemplate' => __DIR__ . '/../..' . '/system/cotemplate.php',
        'Xcache_driver' => __DIR__ . '/../..' . '/system/cache.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb62a2eb53da07861f5b198adce5bd706::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb62a2eb53da07861f5b198adce5bd706::$prefixDirsPsr4;
            $loader->fallbackDirsPsr4 = ComposerStaticInitb62a2eb53da07861f5b198adce5bd706::$fallbackDirsPsr4;
            $loader->classMap = ComposerStaticInitb62a2eb53da07861f5b198adce5bd706::$classMap;

        }, null, ClassLoader::class);
    }
}