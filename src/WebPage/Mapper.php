<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy
 * Date: 18.10.2017
 * Time: 5:54
 */

namespace Helpers\WebPage;


class Mapper
{
    public static function mapMerge(array $map, $options)
    {
        if(!is_array($options))
            return $map;

        $merged = [];
        foreach ($map as $name => $value) {
            if (isset($options[$name]) && !is_array($options[$name])) {
                $merged[$name] = $options[$name];
            }elseif (isset($options[$name]) && is_array($options[$name]) && is_array($map[$name])){
                $merged[$name] = self::mapMerge($map[$name], $options[$name]);
            }elseif (isset($options[$name]) && $map[$name] == null){
                $merged[$name] = $options[$name];
            }else{
                $merged[$name] = $map[$name];
            }
        }
        return $merged;
    }
}