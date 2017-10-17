<?php
/**
 * Created by PhpStorm.
 * User: Дмитрий
 * Date: 27.02.2017
 * Time: 0:58
 */
namespace Helpers\WebPage;

/**
 * Class Parser
 * @package Helpers\WebPage\Parser
 */
class Parser
{
    const CURLOPT_CONNECTTIMEOUT_DEV = 10;
    const CURLOPT_CONNECTTIMEOUT_PROD = 60;

    const PARSER_URL_LOGIN = 'https://www.instagram.com/accounts/login/ajax/';

    public $convert = false;
    public $convertFrom = 'UTF-8';
    public $convertTo = 'UTF-8';

    private $data;

    public $username = 'kuku11111111112';
    private $password = '123454321';

    public $useragent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36';

    public $useCookies = true;
    private $cookies = null;

    public $currentUrl;
    public $nextUrl;

    public $currentPage;

    /**
     * Parser constructor.
     * @param string $data
     * @param bool $useCookies
     * @param null $cookies
     * @param $currentUrl
     */
    public function __construct($currentUrl, $useCookies = false, $cookies = null)
    {
        $this->currentUrl = $currentUrl;
        $this->useCookies = $useCookies;
        $this->cookies = $cookies;
    }

    public function load()
    {
        $this->data = $this->getPage();

        return $this->convert
            ? iconv($this->convertFrom, $this->convertTo, $this->data->saveHTML())
            : $this->data->saveHTML();
    }

    public function getPage($url = null): DOMDocument
    {
        if (is_null($url))
            $url = $this->currentUrl;
        $source = $this->getSource($url);
        $DOM = new DOMDocument();
        libxml_use_internal_errors(true);
        $DOM->loadHTML($source);
        libxml_clear_errors();

        return $DOM;
    }

    public function find(): DOMDocument
    {
        return $this->data;
    }
    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function getSource($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::CURLOPT_CONNECTTIMEOUT_DEV);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        if($this->useCookies){
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
        }

        return curl_exec($curl);
    }

    public function login()
    {
        $curl = curl_init(self::PARSER_URL_LOGIN);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::CURLOPT_CONNECTTIMEOUT_DEV);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, false);
        curl_setopt($curl, CURLOPT_POST, 'username='.$this->username.'&password='.$this->password);
        if($this->useCookies){
            curl_setopt($curl, CURLOPT_COOKIESESSION, true);
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
            return $data;
        }

        return curl_exec($curl);
    }

    private function parseCookies($cookies = []): array
    {
        $collections = [];
        foreach ($cookies as $cookie){
            //PHPSESSID=jnt0kfrf77i20lhajq907j5e40; path=/; domain=.kinogo.club; HttpOnly
            if(preg_match('/\s*(.+?)=(.+?);(?: expires=(.+?);)? path=(.+?); domain=(.+?); (HttpOnly)/is', $cookie, $output)){
                $collections[] = [
                    'name'     => $output[1],
                    'value'    => $output[2],
                    'expires'  => $output[3],
                    'path'     => $output[4],
                    'domain'   => $output[5],
                    'httponly' => $output[6],
                ];
            }
        }
        //        var_dump($collection);

        return $collections;
    }

    private function saveCookies($collections)
    {
        if(count($collections) == 0)
            return false;

        foreach ($collections as $collection => $value){
            //exple.org	TRUE	/	FALSE	1490750046	static_files	iy1aBf1JhQR
            switch ($collection){
                case 'name':
                    break;
            }
        }
    }
}
function pre($array)
{
    echo '<pre>';
    print_r($array);
    echo "</pre>";
}