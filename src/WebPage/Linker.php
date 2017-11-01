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
    protected $mapOptions = [
        'https' => true,
        'argsSeparator' => '&',
        'template' => '%s/%s?%s'
    ];

    public function __construct($baseUrl, $template = null, $options = [])
    {
        $this->options = Mapper::mapMerge($this->mapOptions, $options);
        $this->baseUrl = $this->urlize($baseUrl, $this->options['https']);
        $this->template = is_string($template) ? $template : '%s/%s?%s';
    }

    public function link($page = '', $query = null, $options = [])
    {
        $options = Mapper::mapMerge($this->mapOptions, $options);

        $this->last = $this->current;
        $template = is_string($options['template']) ? $options['template'] : $this->template;

        $query = is_array($query) ? $this->_buildQuery($query, $options['argsSeparator']) : $query;
        $this->current = $this->urlize(
            rtrim(
                sprintf($template, $this->baseUrl, $page, $query),
                '&?'
            ),
            $options['https']
        );

        return $this->current;
    }

    protected function _buildQuery($query, $separator = null)
    {
        return http_build_query($query, false, $separator);
    }

    protected function urlize($url, $https = null)
    {
        $parsedUrl = parse_url($url);
        $url = [];
        $url['scheme'] = $https ? 'https://' : 'http://';
        if(key_exists('host', $parsedUrl))
            $url['host'] = $parsedUrl['host'];
        if (key_exists('path', $parsedUrl))
            $url['path']= $parsedUrl['path'];
        if (key_exists('query', $parsedUrl))
            $url['query']= '?' . $parsedUrl['query'];

        $url = join($url);
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