<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy
 * Date: 18.10.2017
 * Time: 2:32
 */

namespace Helpers;


class Response
{
    /** @var \DOMDocument */
    protected $body;
    protected $head;
    protected $info;

    public function __construct($info, $head, \DOMDocument $body)
    {
        $this->info = $info;
        $this->head = $head;
        $this->body = $body;
    }

    /**
     * @return \DOMDocument
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return mixed
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }
}