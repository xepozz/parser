<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy
 * Date: 17.10.2017
 * Time: 4:29
 */
namespace Helpers\WebPage;

use Helpers\Exceptions\Exception;
use Helpers\Exceptions\HTMLException;
use Helpers\Exceptions\JSONException;
use Helpers\Models\HTML;
use Helpers\Models\JSON;
use Helpers\Response;
use Helpers\ResponseHTML;
use Helpers\ResponseJSON;
use Helpers\WebPage\Mapper;

class Loader
{
    protected static $cookie = [];
    private $linker;
    protected static $mapOptions = [
        'format' => 'html',
        'json' => [
            'decode' => true,
            'asArray' => true
        ],
        'post' => null,
        'request' => [
            'json' => false,
        ],
        'cookieFile' => '',
        'headers' => null,
    ];

    public function __construct($baseUrl)
    {
        $this->linker = new Linker($baseUrl);
    }

    public function get($page, $query = null, $options = [])
    {
        $this->linker->link($page, $query, $options);
        //        var_dump($this->linker);
        //        var_dump($options);
        return self::_get($this->linker, $query, $options);
    }

    public static function _get($url, $query = null, $_options = [])
    {
        $options = Mapper::mapMerge(self::$mapOptions, $_options);
        if($url instanceof Linker){
            $link = $url;
        }else{
            $link = new Linker($url, $_options);
            $link->link('', $query);
        }
        $data = self::_load($link->current, $options);

        return self::_response($data, $options);
    }

    private static function _load($url, $options)
    {
        try{
            $curl = curl_init($url);
            if(is_array($options['headers']))
                curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);

            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            //        curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
            //        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::CURLOPT_CONNECTTIMEOUT_DEV);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

            if(isset($options['https']) && $options['https'] == true){
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            }

            curl_setopt($curl, CURLINFO_HEADER_OUT, false);
            if(isset($options['post']) && count($options['post']) > 0){
                $fields = $options['request']['json'] ? http_build_query($options['post']) : JSON::encode($options['post']);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
                //                var_dump($fields);
            }
            if(!empty($options['cookieFile'])){
                if(!file_exists($options['cookieFile']))
                    touch($options['cookieFile']);

                $cookies = file_get_contents($options['cookieFile']);
                self::$cookie = JSON::decode($cookies, true);
                //                var_dump(self::$cookie);
                curl_setopt($curl, CURLOPT_COOKIESESSION, true);
                //                curl_setopt($curl, CURLOPT_COOKIEFILE, $options['cookieFile']);
                //                curl_setopt($curl, CURLOPT_COOKIEJAR, $options['cookieFile']);
                $cookie = is_array(self::$cookie) ? http_build_query(self::$cookie, '', '; ') : self::$cookie;
                var_dump($cookie);
                curl_setopt($curl, CURLOPT_COOKIE, $cookie);
            }
            $response = curl_exec($curl);
            $responseInfo = curl_getinfo($curl);
            if(preg_match_all('/Set-Cookie: (.*?)=(.*?);(.*?);/', $response, $cookies)) {
                if(count($cookies[1]) == count($cookies[2]))
                {
                    $_cookies = [];
                    foreach ($cookies[1] as $key => $val) {
                        $_cookies[$val] = $cookies[2][$key];
                    }
                    if(count($_cookies) > 0)
                        self::$cookie = array_merge(self::$cookie, $_cookies);
                }
            }
            self::cookieSave($options['cookieFile']);
            //            var_dump(self::$cookie);
            return [
                'response' => $response,
                'info' => $responseInfo,
            ];
        }catch (Exception $exception)
        {
            throw new Exception("Can't get content from url: " . $url);
        }
    }

    /**
     * @param array $data
     * @param array $options
     * @return mixed
     * @throws Exception
     * @throws HTMLException
     * @throws JSONException
     */
    private static function _response(array $data, array $options)
    {
        try{
            $head = trim(
                str_replace(
                    "\r\n\r\n",
                    "\r\n",
                    substr($data['response'], 0, $data['info']['header_size'])
                )
            );
            $html = substr($data['response'], $data['info']['header_size']);

            //            var_dump($html);
            $body = '';
            $responseClass = "\Helpers\\";
            if($options['format'] === 'json') {
                $responseClass .= 'ResponseJSON';
                if ($options['json']['decode'] == true)
                    $body = JSON::decode($html);
            }else{
                $responseClass .= 'ResponseHTML';
                $body = new HTML();
                @$body->loadHTML($html);
            }
            return new $responseClass(
                $data['info'],
                explode("\r\n", $head),
                $body
            );
        }catch (JSONException $exception){
            throw new JSONException(json_last_error_msg(), json_last_error());
        }catch (HTMLException $exception){
            throw new HTMLException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }catch (Exception $exception){
            throw new Exception($exception->getMessage(), $exception->getCode());
        }
    }

    private static function cookieSave($file)
    {
        return file_put_contents($file, JSON::encode(self::$cookie));
    }

    public static function getCookie()
    {
        return self::$cookie;
    }



}