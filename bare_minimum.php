<?php declare( strict_types = 1 );

require_once __DIR__ . "/PegasusICT/HtmlFactory/system/HtmlFactory.class.php";

use PegasusICT\HtmlFactory\HtmlFactory;
// bare minimum:
$html = new HtmlFactory();
$html->render();
