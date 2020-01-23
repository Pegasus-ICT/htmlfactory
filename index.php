<?php declare( strict_types = 1 );
// minimalist scenario
require_once __DIR__ . "/vendor/pegasusict/htmlfactory.class.php";
use pegasusict\HtmlFactory;
$config=["mysites.ini","mysite1.ini"]; // ini files can be 'stacked', overlapping values are overwritten.
$html = new HtmlFactory($config);
$html->render();
