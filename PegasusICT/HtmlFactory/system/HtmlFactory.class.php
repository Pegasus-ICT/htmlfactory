<?php declare( strict_types=1 );
/**
 * @package HtmlFactory
 * @author Mattijs Snepvangers, pegasus.ict@gmail.com
 *
 * @copyright Pegasus ICT Dienstverlening 2002-2020
 * @license MIT
 * @see http://github.com/pegasusict/htmlfactory/
 */
namespace {
    if(PHP_MAJOR_VERSION . PHP_MINOR_VERSION < 71) {   throw new ("I need at least PHP 7.1 to function properly!!!"); }
}
namespace PegasusICT\HtmlFactory{

    use PegasusICT\HtmlFactory\HtmlConstants as HC;

    /**
     * Class HtmlFactory
     * When initializing a new object using one or more ini files, make sure these are located in __DOCROOT__/cfg/
     * e.g. $html = new HtmlFactory(mySites.ini, myFirstSite.ini);
     *
     * @package pegasusict\HtmlFactory
     */
    class HtmlFactory {
        private $_startTime;
        private $_stack=[];
        private $_languages       = [ HC::LANG_EN, HC::LANG_NL ];
        public  $RequestedLanguageID = -1;
        private $_pages=[];

        /**
         * HtmlFactory constructor.
         *
         * @param mixed ...$ConfigFiles
         *
         * @return \PegasusICT\HtmlFactory\HtmlFactory
         */
        public function __construct(...$ConfigFiles) {
            $this->_startTime = microtime(); // Start counting
            $this->_init($ConfigFiles)->_parseRequests();

            return $this;
        }

        /**
         * @param array $pHeaderFields
         * @param bool  $pAppend
         *
         * @return \PegasusICT\HtmlFactory\HtmlFactory
         */
        public function setHeader(array $pHeaderFields, bool $pAppend=false):HtmlFactory {
            foreach(HC::HEADER_FIELDS as $lField) {
                if(is_array($this->_stack['header'][$lField])){
                    if($pAppend == 1)
                        $this->_stack['header'][$lField][] = $pHeaderFields[$lField];
                    else
                        $this->_stack['header'][$lField] = $pHeaderFields[$lField];
                } elseif(!empty($this->_stack['header'][$lField]) && $pAppend == 1) {
                    $this->_stack['header'][$lField] .= $pHeaderFields[$lField];
                }
                $this->_stack['header'][$lField] = $pHeaderFields[$lField];
            }
            return $this;
        }

        /**
         * @param array $pages per page: pageID => [ lang => title,lang => title ]
         *
         * @throws \Exception
         */
        public function setPages( array $pages ) {
            foreach( $pages as $pageID => $pageTitles ) $this->_pages[$pageID] = new HtmlPage($pageTitles);
        }

        /**
         * @return \PegasusICT\HtmlFactory\HtmlFactory
         */
        private function _getLangRequest(): HtmlFactory {
            $lLang = @$_GET['lang'] ?? locale_accept_from_http( $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) ?? HC::LANG_EN;
            $lLang = in_array( $lLang, $this->_languages, false ) ? $lLang : HC::LANG_EN;
            $this->RequestedLanguageID = array_flip($this->_languages )[ $lLang ];

            return $this;
        }

        /**
         * @param array $pLanguages
         * @param bool  $pReplace
         *
         * @return \PegasusICT\HtmlFactory\HtmlFactory
         */
        public function addLanguages( array $pLanguages, $pReplace=false ):HtmlFactory {
            if($pReplace) $this->_languages = $pLanguages;
            else $this->_languages = array_merge( $this->_languages, $pLanguages );

            return $this;
        }

        /**
         * @param array $pTitles
         *
         * @return \PegasusICT\HtmlFactory\HtmlFactory
         */
        public function setSiteTitle( array $pTitles ):HtmlFactory {
            foreach( $this->_languages as $language ) $this->_stack[ $language ][ 'title' ] = $pTitles[ $language ] && null;

            return $this;
        }

        /**
         * @param array $ConfigFiles
         * @param bool  $append
         *
         * @return \PegasusICT\HtmlFactory\HtmlFactory
         */
        private function _init( $ConfigFiles=[], $append=false ):HtmlFactory {
            $lConfig = [];
            if(empty($ConfigFiles)) $ConfigFiles = [ "HtmlFactory_demo.ini" ];
            $lFiles = array_merge( [ "HtmlFactory.ini" ] ,$ConfigFiles );
            foreach( $lFiles as $lFile ) {
                if(file_exists($lFile)) {
                    $lParsedFile = parse_ini_file( $lFile, true );
                    if($append) {
                        $lConfig = array_merge_recursive($lConfig, $lParsedFile);
                    } else {
                        $lConfig = array_replace_recursive($lConfig, $lParsedFile);
                    }
                }
            }
            // basic html5 structure
            $this->_stack = [
                'doctype' => "<!DOCTYPE html>",
                'html'    => [
                    'attributes'=>['lang'=>'en','charset'=>'utf-8'],
                    'head'    => [
                        'application-name' => HC::APP_NAME,
                        'author'           => HC::AUTHOR,
                        'charset'          => 'UTF-8',
                        'content-type'     => 'text/html',
                        'description'      => '',
                        'generator'        => HC::AUTHOR['organisation'] . " " . HC::APP_NAME,
                        'js'               => [],
                        'keywords'         => [ HC::APP_NAME, HC::AUTHOR['organisation'] ],
                        'language'         => '',
                        'link'             => '',
                        'title'            => '',
                        'viewport'         => ''
                    ],
                    'body'    => [
                        'header' => [
                            'nav' => []
                        ],
                        'content' => [],
                        'footer' => []
                    ]
                ]
            ];
            $this->html = "";
            return $this;
        }

        public function setHeaders(array $headerfields) {
            $lHeader='';
            foreach($headerfields as $headerfield) {
                $headerfield = explode("=", $headerfield, 2);
                    $lHeader    .= "  ";
                if(!$headerfield[0]) return;
                switch($headerfield[0]) {
                    case "description":
                    case "author":
                    case "generator":
                    case "keywords":
                    case "application-name":
                    case "viewport":
                        $lHeader .= self::meta("name=" . $headerfield[0], "content=" . $headerfield[1], self::RETURN) . HC::NL;
                        break;
                    case "charset":
                        $lHeader .= self::meta("http-equiv=Content-Type", "content=text/html;charset=" . $headerfield[1], self::RETURN) . HC::NL;
                        break;
                    case "language":
                    case "refresh":
                        $lHeader .= self::meta("http-equiv=" . $headerfield[0], "content=" . $headerfield[1], self::RETURN) . HC::NL;
                        break;
                    case "nocache":
                        $lHeader .= self::meta("http-equiv=cache-control", "content=no-cache", self::RETURN) . HC::NL;
                        break;
                    case "title":
                        $lHeader .= self::title("txt=" . $headerfield[1], self::RETURN) . HC::NL;
                        break;
                    case "css":
                        $lHeader .= self::link("rel=stylesheet", "href=" . $headerfield[1], self::RETURN) . HC::NL;
                        break;
                    case "icon":
                        $lHeader .= self::link("rel=shortcut icon", "href=" . $headerfield[1], self::RETURN) . HC::NL;
                        $lHeader .= self::link("rel=icon", "href=" . $headerfield[1], self::RETURN) . HC::NL;
                        break;
                    case "meta":
                    case "link":
                        $lHeader .= self::_singleRow($headerfield[0] . "|" . $headerfield[1] . "|ret") . HC::NL;
                        break;
                    case "script":
                        $lHeader .= self::script(self::TYPE_JS, null, $headerfield[1], self::RETURN);
                        break;
                    case "js":
                        $lHeader .= self::script(self::TYPE_JS, $headerfield[1], null, self::RETURN);
                        break;
                    default:
                        $lHeader .= self::_tag(array_merge(func_get_args(), [HC::RETURN])) . HC::NL;
                        break;
                }
            }
            if(array_key_exists('html',$this->_stack)
               && array_key_exists('head',$this->_stack['html'])
               && is_array($this->_stack['html']['head'])) {
                $this->_stack['html']['head'] = array_merge_recursive($this->_stack['html']['head'], $lHeader);
            } else {
                $this->_stack['html']['head'] = $lHeader;
            }
        }

        public function render() {
            $this->_validate();
            $result  = "<!DOCTYPE html>\n";
            $this->_getLangRequest();
            $result .="";

            return $result;
        }

        /**
         * @param $pName
         * @param $pArguments
         *
         * @return string|void
         */
        public function __call( $pName, $pArguments ) {
            return ( self::_tag( array_merge( [ $pName ], $pArguments ) ) );
        }

        /**
         * @return \PegasusICT\HtmlFactory\HtmlFactory|string|void
         */
        private function _tag() {
            $lReturn = $lInnerHtml = $lArguments = $lClosingSlash = NULL;
            $lParams = func_get_args();
            $lParams = ( is_array( $lParams[ 0 ] ) ) ? $lParams[ 0 ] : $lParams;
            if( ! $lParams ) return;  # No parameters, end
            if( in_array($lParams[ 0 ], HC::ELEMENTS_INPUT, TRUE ) ) {
                $lParams[]    = 'type=' . $lParams[ 0 ];
                $lParams[ 0 ] = "input";
            } else {
                $lClosingSlash = in_array($lParams[ 0 ], HC::ELEMENTS_SINGLE, TRUE ) ? " /" : NULL;
            }
            for( $lIndex = 1, $lCount = count( $lParams ); $lIndex < $lCount; $lIndex++ ) {
                if( ! $lParams[ $lIndex ] ) {
                    continue;
                }
                $lAttribute = @explode( "=", $lParams[ $lIndex ], 2 );
                if( $lAttribute[ 0 ] === "txt" ) {
                    $lInnerHtml = sprintf( "%s</%s>", $lAttribute[ 1 ], $lParams[ 0 ] );
                } elseif( $lAttribute[ 0 ] === "noclose" ) {
                    $lClosingSlash = " /";
                } elseif($lAttribute[ 0 ] === HC::RETURN ) {
                    $lReturn = TRUE;
                } else {
                    $lArguments .= sprintf( ' %s="%s"', $lAttribute[ 0 ], $lAttribute[ 1 ] );
                }
            }
            # Generate front spaces in develop mode and EOL
            $lIndent    = ( ( DEVELOP_MODE ) && ! $lReturn ) ? str_repeat( " ", count( $this->_stack ) ) : NULL;
            $lEndOfLine = ( ( DEVELOP_MODE ) && ! $lReturn ) ? HC::NL : NULL;
            $lHtml      = sprintf( "%s<%s%s%s>%s%s", $lIndent, $lParams[ 0 ], $lArguments, $lClosingSlash, $lInnerHtml,
                                   $lEndOfLine ); # Assembly generated HTML row

            # Register current tag into stack if closable and if txt not present
            if( ! $lClosingSlash && ! $lInnerHtml ) $this->_stack[ count( $this->_stack ) ] = $lParams[ 0 ];

            # Display or return generated HTML row
            if( $lReturn ) return $lHtml;

            echo $lHtml;
            return $this;
        }

        /**
         * Parses _ENV/_GET/_POST/_PUT/_COOKIE/_SESSION variables and stores the results in the Object
         */
        private function _parseRequests() {
            foreach( $_SERVER as $key ) {
                if( in_array( $key, [ 'PHP_SELF', 'argv', 'argc', 'SERVER_ADDR', 'SERVER_NAME',
                    'REQUEST_METHOD' => [ 'GET', 'HEAD',
                        'POST', 'PUT' ], 'REQUEST_TIME', 'REQUEST_TIME_FLOAT', 'QUERY_STRING', 'DOCUMENT_ROOT',
                    'HTTP_ACCEPT',
                    'HTTP_USER_AGENT', 'HTTPS', 'REMOTE_PORT', 'REMOTE_USER', 'SCRIPT_FILENAME' ] ) ) {
                    ;
                }
            }
            foreach( $_GET  as $key => $value ) $this->_get[$key]  = $value;
            foreach( $_POST as $key => $value ) $this->_post[$key] = $value;
        }

        /**
         * @param string $pLanguage
         *
         * @return HtmlFactory
         * @throws \PegasusICT\HtmlFactory\BaseException
         */
        public function setDefaultLanguage(string $pLanguage) {
            if(!in_array($pLanguage,$this->_languages))
                throw new BaseException("I'm sorry, $pLanguage is an unknown language to me.", BaseException::XPCT_UNKN_LANG);
            $this->_defaultLanguage = $pLanguage;

            return $this;
        }

        private function _validate():HtmlFactory {
            // todo: make sure all necessary data is complete and valid

            return $this;
        }

    }
}