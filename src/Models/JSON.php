<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy
 * Date: 18.10.2017
 * Time: 6:02
 */

namespace Helpers\Models;

use Helpers\Exceptions\JSONException;

class JSON
{

    /**
     * @param $string
     * @return mixed
     * @throws JSONException
     */
    public static function decode($string)
    {
        try{
            return json_decode($string);
        }catch (JSONException $exception)
        {
            throw new JSONException(json_last_error_msg(), json_last_error());
        }
    }

    /**
     * @param $string
     * @return string
     * @throws JSONException
     */
    public static function encode($string)
    {
        try{
            return json_encode($string);
        }catch (JSONException $exception)
        {
            throw new JSONException(json_last_error_msg(), json_last_error());
        }
    }
}