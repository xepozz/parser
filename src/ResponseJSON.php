<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy
 * Date: 18.10.2017
 * Time: 2:43
 */

namespace Helpers;


class ResponseJSON extends Response
{
    /**
     * @return \JsonSerializable
     */
    public function getBody()
    {
        return json_decode(parent::getBody());
    }

}