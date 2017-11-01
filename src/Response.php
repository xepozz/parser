<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy
 * Date: 18.10.2017
 * Time: 2:32
 */

namespace Helpers;


use Helpers\Exceptions\Exception;
use DOMException;
use Helpers\Models\HTML;

class Response
{
    protected $body;
    protected $head;
    protected $info;

    /**
     * Response constructor.
     * @param $info
     * @param $head
     * @param $body
     */
    public function __construct($info, $head, $body)
    {
        try{
            $this->info = $info;
            $this->head = $head;
            $this->body = $body;
        }catch (Exception $exception){
            throw new Exception(json_last_error_msg(), json_last_error());
        }catch (DOMException $exception){
            throw new DOMException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    /**
     * Return pages body <html> etc
     * @return HTML|mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return meta information from request to site
     * @return mixed
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * Return specific CURL meta information from request to site
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }
}