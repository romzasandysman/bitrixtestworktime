<?php
namespace My\Worktime;

use Bitrix\Main\Loader;

class Main
{
    private static $MODULE_NAME = 'my.worktime';

    public static function selfLoad() : bool
    {
        return Loader::includeModule(self::$MODULE_NAME);
    }
}