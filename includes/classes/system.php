<?php
class system
{
    private static $objects;

    public static function Set($key,$value)
    {
        self::$objects[$key]= $value;
    }

    public static function Get($key)
    {
        if(isset(self::$objects[$key]))
        {
            return self::$objects[$key];
        }
        return null;
    }
}