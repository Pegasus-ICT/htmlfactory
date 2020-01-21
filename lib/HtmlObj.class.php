<?php
const DS = DIRECTORY_SEPARATOR;
class HtmlObj {
    private $_html;
    private $_languages= [ self::LANG_EN, self::LANG_NL ];
    public static $debugMode = false;

    private const BRYTHON_LIBS=[ "brython", "brython_stdlib"];
    private const BLUEPRINT = [
        'doctype' => "<!DOCTYPE html>"
    ,   'head'=>[
            'application-name'=>null,'author'=>null,
            'charset' => 'UTF-8',
            'content-type' => 'text/html',
            'css' => [],
            'description' => null,
            'generator' => 'Pegasus ICT HtmlObj',
            'icon' => null,
            'js' => [],
            'keywords' => null,
            'language' => null,
            'link'=>null,'meta'=>null,'nocache'=>null,'refresh'=>null,'style'=>null,'title'=>null,'viewport'=>null]
    ,   'body'=>['header'=>['nav'=>null],'content'=>[],'footer'=>[]]
    ];
    private const DEFAULTS = [
        'prefs'=>[
            'paths'=>['js'=>'js'.DS,'py'=>'py'.DS,'css'=>'css'.DS,'img'=>'img'.DS]
        ,   'exts' =>[ 'js' => 'js', 'py' => 'py', 'css' => 'css']
        ,   'enabled'=>['js'=>['brython','bootstrap'],'css'=>['bootstrap']]
        ]
        ,   'html'=>[]
    ];
    public const LANG_EN = "en";
    public const LANG_NL = "nl";

    public function __construct() {
        $this->_html = self::BLUEPRINT; // populate basic structure
    }

    public function getLangRequest(): int {
        $lLang = array_key_exists('lang',$_GET) ? $_GET['lang'] : locale_accept_from_http( $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        return array_flip($this->_languages)[$lLang];
    }
    public function addLanguages( array $pLanguages ): void {
        $this->_languages .= $pLanguages;
    }

    private function _init() {

    }

    private function _validate() {}
    public function render() {}
}