<?php
/**
 * Created by PhpStorm.
 * User: Дмитрий
 * Date: 27.02.2017
 * Time: 1:15
 */
/*
require 'Parser.php';
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';

$parser = new Parser('http://instagram.com', true);
$parser->convert = fallse;
$parser->load();
$parser->login();
$parser->getPage('https://instagram.com');
$data = $parser->find();
$page = $data->getElementById('dle-content');
*/
//pre($page->ownerDocument->saveHTML($page));
//echo strtotime('27-Feb-18 01:46:45');
//echo '<br>';
//echo strtotime('28-Feb-2016 GMT');
require_once 'vendor/autoload.php';

use Helpers\WebPage\Linker;

$link = new Linker("google.com");
echo $link->link();
//echo $link->link("index.php", ["page" => 10]);
