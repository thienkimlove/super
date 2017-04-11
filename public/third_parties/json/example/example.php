<?php
require_once dirname(__FILE__) . '/../vendor/autoload.php';
$url = $_GET['url'];
$listener = new \JsonStreamingParser\Listener\InMemoryListener();
$stream = fopen($url, 'r');
try {
    $parser = new \JsonStreamingParser\Parser($stream, $listener);
    $parser->parse();
    fclose($stream);
} catch (Exception $e) {
    fclose($stream);
    throw $e;
}

$rand = uniqid().'_'.time().'.txt';
file_put_contents(dirname(__FILE__) . '/../../../../storage/logs/'.$rand, json_encode($listener->getJson(), true));
echo $rand;
