<?php
namespace PegasusICT\HtmlFactory;
use const \DIRECTORY_SEPARATOR;

class HtmlConstants extends FieldTypes {
    public const COPYRIGHT = 'Copyright © Mattijs Snepvangers, Pegasus ICT Dienstverlening 2019-2020';
    public const ENTITIES  = [
        'elements'=>['html','head','meta','lang','link','style','script'],
        'structure' => [
            'html'=>['attributes'=>['lang']],
            'head'=>['title','base','meta'=>['charset','lang'] ],
            'body'=>[],
        ]
    ];
    public const  LANG_EN    = "en";
    public const  LANG_NL    = "nl";
    public const  LIBS        = [
        'brython' => [
            'js' => [
                "brython.js",
                "brython_stdlib.js"
            ]
        ],
        'bootstrap' => [
            'js'=>[],
            'css'=>[]
        ]
    ];
    public const RETURN       = "ret";
    public const APP_NAME     = 'Html Factory';
    public const AUTHOR          = [
        'name'=> "Mattijs Snepvangers",
        'organisation'=> "Pegasus ICT Dienstverlening"
    ];
    public const DS              = DIRECTORY_SEPARATOR;
    public const NL              = "\n";
    public const ELEMENTS_SINGLE = [
        "img", "input", "br", "hr", "param", "meta", "link", "base", "frame", "embed", "area"];
    public const HEADER_FIELDS   = ['application-name', 'author', 'charset', 'content-type', 'css', 'description', 'generator', 'icon', 'js', 'keywords', 'language', 'link', 'meta', 'nocache', 'refresh', 'style', 'title', 'viewport'];
    public const ELEMENTS_INPUT  = [
        "checkbox", "file", "hidden", "image", "password", "radio", "reset", "submit", "text", "date", "number", "range", "slider"];
}