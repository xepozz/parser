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
    private $linker;
    protected static $mapOptions = [
        'format' => 'html',
        'json' => [
            'decode' => true,
            'asArray' => true
        ],
        'post' => null,
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
        $data = self::_load($link->current, $options["post"]);

        return self::_response($data, $options);
    }

    private static function _load($url, $post = null)
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
            if($post){
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            }
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
                $body->loadHTML($html);
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

}