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
    protected $page = null;

    protected $template = null;
    protected $options = null;

    public function __construct($baseUrl, $page = null, $template = '%s/%s?%s', $options = [])
    {
        $this->baseUrl = $baseUrl;
        $this->page = $page;
        $this->template = $template;
        $this->options = $options;
    }

    public function link($page = '', $query = null, $argsSeparator = '&', $_template = null)
    {
        $this->last = $this->current;
        $template = $_template ? $_template : $this->template;

        $query = is_array($query) ? $this->_buildQuery($query, $argsSeparator) : $query;
        $this->current = rtrim(sprintf($template, $this->baseUrl, $page, $query), '&?');

        return $this->current;
    }

    protected function _buildQuery($query, $separator = null)
    {
        return http_build_query($query, false, $separator);
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