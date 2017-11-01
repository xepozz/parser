<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy
 * Date: 18.10.2017
 * Time: 2:43
 */

namespace Helpers;


use Helpers\Models\HTML;

class ResponseHTML extends Response
{
    /** @var HTML */
    public $body;

    public function __construct($info, $head, \DOMDocument $body)
    {
        parent::__construct($info, $head, $body);

        $this->info = $info;
        $this->head = $head;
        $this->body = $body;
    }
    /**
     * @return \String|null
     */
    public function getBody()
    {
        $html = parent::getBody();
        return is_object($html) ? $html->saveHTML() : null;
    }


}