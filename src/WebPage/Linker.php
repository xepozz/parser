<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy
 * Date: 17.10.2017
 * Time: 5:06
 */

namespace Helpers\WebPage;


class Linker
{
    public $last = null;
    public $current = null;

    protected $baseUrl = null;

    protected $template = null;
    protected $options = null;

    public function __construct($baseUrl, $template = null, $options = [])
    {
        if(!key_exists('https', $options))
            $options['https'] = true;

        $this->baseUrl = $this->urlize($baseUrl, $options['https']);
        $this->template = is_string($template) ? $template : '%s/%s?%s';
        $this->options = $options;
    }

    public function link($page = '', $query = null, $argsSeparator = '&', $_template = null)
    {
        $this->last = $this->current;
        $template = is_string($_template) ? $_template : $this->template;

        $query = is_array($query) ? $this->_buildQuery($query, $argsSeparator) : $query;
        $this->current = rtrim(sprintf($template, $this->baseUrl, $page, $query), '&?');

        return $this->current;
    }

    protected function _buildQuery($query, $separator = null)
    {
        return http_build_query($query, false, $separator);
    }

    protected function urlize($url, $https = null)
    {
        $parsedUrl = parse_url($url);
        $url = false;
        $scheme = $https ? 'https://' : 'http://';
        if(key_exists('host', $parsedUrl))
            $url = $scheme . $parsedUrl['host'];
        elseif (key_exists('path', $parsedUrl))
            $url = $scheme . $parsedUrl['path'];

        return $url;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($newTemplate)
    {
        return $this->template = $newTemplate;
    }
}