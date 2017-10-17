<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy
 * Date: 17.10.2017
 * Time: 4:29
 */
namespace Helpers\WebPage;

use Helpers\Exceptions\Exception;
use Helpers\Response;

class Loader
{
    private $link;

    public function __construct($baseUrl)
    {
        $this->link = new Linker($baseUrl);
    }

    /**
     * @param $page
     * @param null $query
     * @param bool $decode
     * @param bool $asArray
     * @return Response
     */
    public function load($page, $query = null, $decode = false, $asArray = true)
    {
        $this->link->link($page, $query);
        $data = self::_load($this->link->current);

        return self::_response($data, $decode, $asArray);
    }

    public static function get($url, $query = null, $decode = false, $asArray = true)
    {
        $link = new Linker($url);
        $link->link('', $query);
        $data = self::_load($link->current);

        return self::_response($data, $decode, $asArray);
    }
    private static function _load($url)
    {
        try{
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            //        curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
            //        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::CURLOPT_CONNECTTIMEOUT_DEV);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLINFO_HEADER_OUT, false);
            /*if($this->useCookies){
                curl_setopt($curl, CURLOPT_COOKIESESSION, true);
                //            var_dump($this->cookies);
                curl_setopt($curl, CURLOPT_COOKIE,
                    (is_array($this->cookies)
                        ? implode('; ', $this->cookies)
                        : null
                    )
                );
                $data = curl_exec($curl);
                $info = curl_getinfo($curl);
                $errors = curl_error($curl);
                curl_close($curl);

                if(preg_match_all('/Set-Cookie: (.*?);/', $data, $cookies)) {
                    $this->cookies = $cookies[1];
                }
                //            var_dump($this->cookies);
                //            var_dump($info);

                return $data;
            }*/
            $response = curl_exec($curl);
            $responseInfo = curl_getinfo($curl);
            $head = trim(
                str_replace(
                    "\r\n\r\n",
                    "\r\n",
                    substr($response, 0, $responseInfo['header_size'])
                )
            );
            $body = new \DOMDocument;
            $body->loadHTML(substr($response, $responseInfo['header_size']));
            return new Response(
                $responseInfo,
                explode("\r\n", $head),
                $body
            );
        }catch (Exception $exception)
        {
            throw new Exception("Can't get content from url: " . $url);
        }
    }
    private static function _response($data, $decode, $asArray)
    {
        try{
            return $decode ? json_decode($data, $asArray) : $data;
        }catch (Exception $exception){
            throw new Exception(json_last_error_msg(), json_last_error());
        }
    }
}