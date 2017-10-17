<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy
 * Date: 17.10.2017
 * Time: 4:29
 */
namespace Helpers\WebPage;

use Helpers\Exceptions\Exception;

class Loader
{
    public static function get($page, $query = null, $decode = false, $asArray = true)
    {
        $link = new Linker($page);
        $link->link('', $query);
        $data = self::_load($link->current);

        return $decode ? json_decode($data, $asArray) : $data;
    }
    private static function _load($url)
    {
        try{

            return file_get_contents($url);
        }catch (\Exception $exception)
        {
            throw new Exception("Can't get content from url: " . $url);
        }
    }

}