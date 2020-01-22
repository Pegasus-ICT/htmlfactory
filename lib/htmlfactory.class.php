<?php declare( strict_types=1 );
namespace{
    if( PHP_MAJOR_VERSION . PHP_MINOR_VERSION < 73 ) die( "I need at least PHP 7.3 to function properly!!!" );
    const DS      = DIRECTORY_SEPARATOR;
    define( 'COPYRIGHT' ,'Copyright © Mattijs Snepvangers, Pegasus ICT Dienstverlening 2019-'.date('Y'));
}

namespace pegasusict {
    use function in_array;
    use function locale_accept_from_http;

    class HtmlFactory {
        public const LANG_EN   = "en";
        public const LANG_NL   = "nl";
        private $_htmlStack;
        private $_html;
        private $_languages = [ self::LANG_EN, self::LANG_NL ];
        public const  BRYTHON_LIBS = [ "brython", "brython_stdlib" ];
        private const BLUEPRINT = [
            'doctype' => "<!DOCTYPE html>",
            'head'    => [
                'application-name' => 'Pegasus ICT Html Factory',
                'author'           => "Mattijs Snepvangers",
                'charset'          => 'UTF-8',
                'content-type'     => 'text/html',
                'description'      => '',
                'generator'        => 'Pegasus ICT Html Factory',
                'icon'             => '',
                'js'               => [],
                'keywords'         => [ "Html Factory", "Pegasus ICT Dienstverlening" ],
                'language'         => '',
                'link'             => '',
                'title'            => '',
                'viewport'         => '' ],
            'body'    => [ 'header'  => [ 'nav' => [] ], 'content' => [], 'footer'  => [] ]
        ];
        public const DEFAULT_SETTINGS = [
            'prefs' => [
                'paths'   => [ 'js' => 'js' . DS, 'py' => 'py' . DS, 'css' => 'css' . DS, 'img' => 'img' . DS ],
                'exts'    => [ 'js' => 'js', 'py' => 'py', 'css' => 'css' ],
                'enabled' => [ 'js' => [ 'brython', 'bootstrap' ], 'css' => [ 'bootstrap' ] ]
            ]
            ,
            'html'  => []
        ];
        public const HEADER_FIELDS = ['application-name','author','charset','content-type','css','description','generator','icon','js','keywords','language','link','meta','nocache','refresh','style','title','viewport'];

        public function __construct() {
            $this->_htmlStack = self::BLUEPRINT; // populate basic structure
            $this->html="";
        }


        /**
         * @param array $headerfields   key/value pairs as headerfield/value
         */
        public function setHeader( array $headerfields ):void {
            foreach( self::HEADER_FIELDS as $headerfield ) {
                if( is_array($this->_htmlStack['header' ][$headerfield ] ) ) {
                    $this->_htmlStack['header' ][$headerfield ][] = $headerfields[$headerfield ];
                } elseif( ! empty($this->_htmlStack['header' ][$headerfield ] ) ) {
                    $this->_htmlStack['header' ][$headerfield ] .= $headerfields[$headerfield ];
                } else {
                    $this->_htmlStack['header' ][$headerfield ] = $headerfields[$headerfield ];
                }
            }
        }

        private function _getLangRequest(): int {
            $lLang = @$_GET['lang'] ?? locale_accept_from_http( $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) ?? self::LANG_EN;
            $lLang = in_array( $lLang, $this->_languages, false ) ? $lLang : self::LANG_EN;

            return array_flip( $this->_languages )[ $lLang ];
        }

        public function addLanguages( array $pLanguages ): void {
            $this->_languages .= $pLanguages;
        }

        /**
         * @param array $pTitles array of titles, indexed by language
         */
        public function setSiteTitle( array $pTitles ): void {
            foreach( $this->_languages as $language ) {
                $this->_htmlStack[$language ]['title' ] = $pTitles[$language ] && null;
            }
        }

        private function _init() { }

        private function _validate() { }

        private function setHeaders(array $headerfields) {
            $lHeader='';
            foreach($headerfields as $headerfield) {
                $headerfield = explode("=", $headerfield, 2);
                    $lHeader    .= "  ";
                if( ! $headerfield[0]) return;
                switch($headerfield[0]) {
                    case "description":
                    case "author":
                    case "generator":
                    case "keywords":
                    case "application-name":
                    case "viewport":
                        $lHeader .= self::meta("name=" . $headerfield[0], "content=" . $headerfield[1], self::RETURN) . "\n";
                        break;
                    case "charset":
                        $lHeader .= self::meta("http-equiv=Content-Type", "content=text/html;charset=" . $headerfield[1], self::RETURN) . "\n";
                        break;
                    case "language":
                    case "refresh":
                        $lHeader .= self::meta("http-equiv=" . $headerfield[0], "content=" . $headerfield[1], self::RETURN) . "\n";
                        break;
                    case "nocache":
                        $lHeader .= self::meta("http-equiv=cache-control", "content=no-cache", self::RETURN) . "\n";
                        break;
                    case "title":
                        $lHeader .= self::title("txt=" . $headerfield[1], self::RETURN) . "\n";
                        break;
                    case "css":
                        $lHeader .= self::link("rel=stylesheet", "href=" . $headerfield[1], self::RETURN) . "\n";
                        break;
                    case "icon":
                        $lHeader .= self::link("rel=shortcut icon", "href=" . $headerfield[1], self::RETURN) . "\n";
                        $lHeader .= self::link("rel=icon", "href=" . $headerfield[1], self::RETURN) . "\n";
                        break;
                    case "meta":
                    case "link":
                        $lHeader .= self::_singleRow($headerfield[0] . "|" . $headerfield[1] . "|ret") . "\n";
                        break;
                    case "script":
                        $lHeader .= self::script(self::TYPE_JS, null, $headerfield[1], self::RETURN);
                        break;
                    case "js":
                        $lHeader .= self::script(self::TYPE_JS, $headerfield[1], null, self::RETURN);
                        break;
                    default:
                        $lHeader .= self::_tag(array_merge(func_get_args(), [self::RETURN])) . "\n";
                        break;
                }
            }

        }
        public function render() {
            $this->html = DOCTYPE . "\n";
            self::html();
            self::head();
            print $this->html['head'];
            self::addComment(self::COPYRIGHT . date('Y'));
            self::closeTag();
        }
    }
}