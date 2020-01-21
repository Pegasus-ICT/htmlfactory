<?php declare( strict_types=1 );
namespace{
    if( PHP_MAJOR_VERSION . PHP_MINOR_VERSION < 73 ) {
        die( "I need at least PHP 7.3 to function properly!!!" );
    }

    const DS      = DIRECTORY_SEPARATOR;
}

namespace pegasusict {
    class HtmlFactory {
        public const COPYRIGHT = 'Copyright © Mattijs Snepvangers, Pegasus ICT Dienstverlening 2019-2020';
        public const LANG_EN = "en";
        public const LANG_NL = "nl";

        private $_html;
        private $_languages= [ self::LANG_EN, self::LANG_NL ];

        public const BRYTHON_LIBS=[ "brython", "brython_stdlib"];
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
        public const DEFAULT_SETTINGS = [
            'prefs'=>[
                'paths'     =>['js'=>'js'.DS,'py'=>'py'.DS,'css'=>'css'.DS,'img'=>'img'.DS]
            ,   'exts'      =>[ 'js' => 'js', 'py' => 'py', 'css' => 'css']
            ,   'enabled'   =>['js'=>['brython','bootstrap'],'css'=>['bootstrap']]
            ]
            ,   'html'=>[]
        ];



        public function __construct() {
            $this->_html = self::BLUEPRINT; // populate basic structure
        }

        private function _getLangRequest(): int {
            $lLang = @$_GET['lang'] ?? \locale_accept_from_http( $_SERVER['HTTP_ACCEPT_LANGUAGE']) ?? self::LANG_EN;
            $lLang = \in_array( $lLang,$this->_languages,false)?$lLang:self::LANG_EN;

            return array_flip($this->_languages)[$lLang];
        }

        public function addLanguages( array $pLanguages ): void {
            $this->_languages .= $pLanguages;
        }
        public function setSiteTitle( string $pTitle, string $pLanguage=self::LANG_EN ):void {}
        private function _init() {}
        private function _validate() {}
        public function render() {}
    }
}