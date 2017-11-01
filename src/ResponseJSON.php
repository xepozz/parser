<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy
 * Date: 18.10.2017
 * Time: 2:43
 */

namespace Helpers;


use Helpers\Exceptions\Exception;

class ResponseJSON extends Response
{
    public $body;

    public function __construct($info, $head, $body)
    {
        parent::__construct($info, $head, $body);
    }

    /**
     * @return \JsonSerializable
     */
    public function getBody()
    {
        parent::getBody();
    }

}