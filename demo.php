<?php declare( strict_types = 1 );

require_once __DIR__ . "/PegasusICT/HtmlFactory/system/HtmlFactory.class.php";

use \PegasusICT\HtmlFactory\HtmlFactory;
use \PegasusICT\HtmlFactory\HtmlConstants as HC;

// instantiate new object
$html = new HtmlFactory();

$html->setDefaultLanguage( HC::LANG_EN );

$html->setSiteTitle( [ HC::LANG_EN => "HtmlFactory, at your service ;-)", HC::LANG_NL => "HtmlFactory, tot uw dienst ;-)" ] );

$pages = [
    100 => [ HC::LANG_EN => 'Welcome', HC::LANG_NL => 'Welkom' ],
    200 => [ HC::LANG_EN => 'Tips', HC::LANG_NL => 'Tips' ],
    300 => [ HC::LANG_EN => 'Contact', HC::LANG_NL => 'Contact' ]
];

$html->setPages( $pages );

$html->

