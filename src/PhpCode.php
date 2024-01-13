<?php

namespace Zeus\Async;

use RuntimeException;

/**
 *
 */
class PhpCode
{

    /**
     * @param AsyncInterface $async
     * @return string
     */
    public static function create(AsyncInterface $async): string
    {
        return '<?php
        error_reporting(E_ALL);
        require_once "'.static::getAutoloadPath().'";
        $async=unserialize(\'' . serialize($async) . '\'); echo $async->run();
       ?>';
    }

    /**
     * @return string
     */
    public static function getAutoloadPath(): string
    {
        foreach ([1,2,3] as $level) {
            $dirname = static::getDirectory($level);
            $autoload = $dirname .DIRECTORY_SEPARATOR.'vendor'. DIRECTORY_SEPARATOR . 'autoload.php';

            if (file_exists($autoload)) {
                return $autoload;
            }

        }
        throw new RuntimeException('vendor/autoload.php does not exist');
    }

    /**
     * @param int $level
     * @return string
     */
    private static function getDirectory(int $level = 1): string
    {
        return dirname(__DIR__, $level);
    }
}
