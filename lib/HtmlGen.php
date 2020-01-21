<?php
declare( strict_types=1 );
define( "DEVELOP_MODE", true );

/**
 * Class HtmlGenerator
 *
 * @method static html( ...$args )
 * @method static head( ...$args )
 * @method static meta( ...$args )
 * @method static title( ...$args )
 * @method static link( ...$args )
 * @method static body( ...$args )
 * @method static span( ...$args )
 * @method static div( ...$args )
 * @method static nav( ...$args )
 * @method static h1( ...$args )
 * @method static h2( ...$args )
 * @method static h3( ...$args )
 * @method static h4( ...$args )
 * @method static h5( ...$args )
 * @method static h6( ...$args )
 * @method static a( ...$args )
 * @method static p( ...$args )
 * @method static ul( ...$args )
 * @method static ol( ...$args )
 * @method static li( ...$args )
 * @method static button( ...$args )
 * @method static code( ...$args )
 * @method static textarea( ...$args )
 */
class HtmlGen {
    public const  DISPLAY         = "display";
    public const  INPUT           = "input";
    public const  CONTENT         = "content=";
    public const  INDENT          = "  ";
    public const  NAME_IS         = "name=";
    public const  RETURN          = "ret";
    public const  EQUIV           = "http-equiv=";
    public const  TYPE_JS         = "type=text/javascript";
    public const  TYPE_PY         = "type=text/python";
    public const  TYPE_CSS        = "type=text/css";
    private const START_BLOCK     = "stpt";
    private const NOT_CLOSABLE    = [ "img", "input", "br", "hr", "param", "meta", "link", "base", "frame", "embed", "area" ];
    private const ACTIVE_ELEMENTS = [ "checkbox", "file", "hidden", "image", "password", "radio", "reset", "submit", "text", "date", "number", "range", "slider" ];

    private static $_stack = [];
    private static $_jsMsg;
    private const COPYRIGHT = 'Copyright © Mattijs Snepvangers ~ Pegasus ICT Dienstverlening  2019-';

    /**
     * @param $pName
     * @param $pArguments
     *
     * @return string|void
     */
    public static function __callStatic( $pName, $pArguments ) {
        return ( self::_tag( array_merge( [ $pName ], $pArguments ) ) );
    }

    private static function _tag() {
        $lReturn = $lInnerHtml = $lArguments = $lClosingSlash = null;
        $lParams = func_get_args();
        $lParams = ( is_array( $lParams[0] ) ) ? $lParams[0] : $lParams;
        if( !$lParams ) return; # No parameters, end
        if( in_array( $lParams[0], self::ACTIVE_ELEMENTS, true ) ) {
            $lParams[]  = 'type=' . $lParams[0];
            $lParams[0] = self::INPUT;
        } else $lClosingSlash = in_array( $lParams[0], self::NOT_CLOSABLE, true ) ? " /" : null; // Check if tag is not closable
        for( $lIndex = 1, $lCount = count( $lParams ); $lIndex < $lCount; $lIndex++ ) {
            if( !$lParams[ $lIndex ] ) continue;
            $lAttribute = @explode( "=", $lParams[ $lIndex ], 2 );
            if( $lAttribute[0] === "txt" ) $lInnerHtml = sprintf( "%s</%s>", $lAttribute[1], $lParams[0] ); elseif( $lAttribute[0] === "noclose" ) $lClosingSlash = " /";
            elseif( $lAttribute[0] === "ret" ) $lReturn = true;
            else $lArguments .= sprintf( ' %s="%s"', $lAttribute[0], $lAttribute[1] );
        }
        # Generate front spaces in develop mode and EOL
        $lIndent    = ( ( DEVELOP_MODE ) && !$lReturn ) ? str_repeat( " ", count( self::$_stack ) ) : null;
        $lEndOfLine = ( ( DEVELOP_MODE ) && !$lReturn ) ? "\n" : null;
        $lHtml      = sprintf( "%s<%s%s%s>%s%s", $lIndent, $lParams[0], $lArguments, $lClosingSlash, $lInnerHtml, $lEndOfLine ); # Assembly generated HTML row
        # Register current tag into stack if closable and if txt not present
        if( !$lClosingSlash && !$lInnerHtml ) self::$_stack[ count( self::$_stack ) ] = $lParams[0];
        # Display or return generated HTML row
        if( !$lReturn ) echo $lHtml; else return $lHtml;
    }

    /**
     * Close all opened tags
     */
    public static function finish() {
        self::closeTag( -1 );
        if( self::$_jsMsg ) self::script( "txt=alert('" . self::$_jsMsg . "');" );
    }

    /**
     * @param int  $pNumber
     * @param bool $pReturn
     *
     * @return string|void
     */
    public static function closeTag( $pNumber = null, $pReturn = null ) {
        $lHtml   = null;
        $pNumber = $pNumber ?? 1;
        $pReturn = $pReturn ?? false;
        if( $pNumber < 1 ) $pNumber = count( self::$_stack );
        $lIndex = count( self::$_stack ) - 1;
        while( $pNumber-- ) {
            $lHtmlRow = sprintf( "</%s>", self::$_stack[ $lIndex ] );
            if( !$pReturn ) $lHtmlRow = @str_repeat( self::INDENT, $lIndex ) . $lHtmlRow . "\n";
            $lHtml .= $lHtmlRow;
            unset ( self::$_stack[ $lIndex-- ] );
        }

        print ( $pReturn ? null : $lHtml );

        return $pReturn ? $lHtml : null;
    }

    public static function script( $pType = null, $pSrc = null, $pCode = null, $pReturn = null ) {
        if( empty( $pSrc . $pCode ) || ( !empty( $pSrc ) && !empty( $pCode ) ) ) return false;
        $pType   = strtolower( $pType ?? self::TYPE_PY );
        $lReturn = ( $pReturn === self::RETURN );
        $pType   =in_array($pType,["js",self::TYPE_JS])?self::TYPE_JS:self::TYPE_PY;
        $format  = ( !empty( $pSrc ) ) ? '<%1$s %2$s src="%3$s"></%1$s>' : '<%1$s %2$s>%4$s</%1$s>';
        $lHtml   = sprintf( $format, __FUNCTION__, $pType, $pSrc, $pCode )."\n";

        print  ( !$lReturn ? $lHtml : null );

        return ( $lReturn ? $lHtml : null );
    }
    public static function style( string $pSrc=null, string $pCode=null, bool $pReturn=false ) {
        if(empty($pSrc.$pCode) || (!empty($pSrc)&&!empty($pCode))) return "ERROR: ".__FUNCTION__." -  either src or code must be specified!";

        $format = ( !empty( $pSrc ) ) ? '<%1$s %2$s %3$s></%1$s>' : '<%1$s %2$s>%4$s</%1$s>';
        $pSrc   = ( !empty( $pSrc ) ) ? sprintf('src="%s"',$pSrc) : null;

        $lResult = sprintf( $format, [__FUNCTION__, self::TYPE_CSS, $pSrc, $pCode ]);


        if($pReturn) return $lResult; else print $lResult;
    }

    public static function startBlock() {
        self::$_stack[ count( self::$_stack ) ] = self::START_BLOCK;
    }
    public static function endBlock() {
        for( $lIndex = count( self::$_stack )-1; $lIndex > 0; --$lIndex ) if( self::$_stack[ $lIndex ] === self::START_BLOCK ) break;
        if( !$lIndex ) return;
        $lClosing = count( self::$_stack ) - $lIndex - 1;
        if( (bool)$lClosing ) self::closeTag( $lClosing );
        unset ( self::$_stack[ ( count( self::$_stack ) - 1 ) ] );
    }

    public static function setHeader() {
        $lArguments = func_get_args();
        static $lHeader = null;
        foreach( $lArguments as $lArgument ) {
            $lArgument = explode( "=", $lArgument, 2 );
            if( ( $lArgument[0] !== self::DISPLAY ) ) {
                $lHeader    .= "  ";
                $lEndOfLine = "\n";
            } else $lEndOfLine = null;
            if( !$lArgument[0] ) return;
            switch( $lArgument[0] ) {
                case "description":
                case "author":
                case "generator":
                case "keywords":
                case "application-name":
                case "viewport":
                    $lHeader .= self::meta( self::NAME_IS . $lArgument[0], self::CONTENT . $lArgument[1], self::RETURN ) . $lEndOfLine;
                    break;
                case "charset":
                    $lHeader .= self::meta( self::EQUIV . "Content-Type", self::CONTENT . "text/html;charset=" . $lArgument[1], self::RETURN ) . $lEndOfLine;
                    break;
                case "language":
                case "refresh":
                    $lHeader .= self::meta( self::EQUIV . $lArgument[0], self::CONTENT . $lArgument[1], self::RETURN ) . $lEndOfLine;
                    break;
                case "nocache":
                    $lHeader .= self::meta( self::EQUIV . "cache-control", "content=no-cache", self::RETURN ) . $lEndOfLine;
                    break;
                case "title":
                    $lHeader .= self::title( "txt=" . $lArgument[1], self::RETURN ) . $lEndOfLine;
                    break;
                case "css":
                    $lHeader .= self::link( "rel=stylesheet", "href=" . $lArgument[1], self::RETURN ) . $lEndOfLine;
                    break;
                case "icon":
                    $lHeader .= self::link( "rel=shortcut icon", "href=" . $lArgument[1], self::RETURN ) . $lEndOfLine;
                    break;
                case "meta":
                case "link":
                    $lHeader .= self::_singleRow( $lArgument[0] . "|" . $lArgument[1] . "|ret" ) . $lEndOfLine;
                    break;
                case "script":
                    $lHeader .= self::script( self::TYPE_JS, null, $lArgument[1], self::RETURN );
                    break;
                case "js":
                    $lHeader .= self::script(self::TYPE_JS, $lArgument[1],null, self::RETURN );
                    break;
                case self::DISPLAY:
                    print DOCTYPE . "\n";
                    self::html();
                    self::head();
                    print $lHeader;
                    self::addComment(self::COPYRIGHT . date( 'Y' ));
                    self::closeTag();
                    break;
                default:
                    $lHeader .= self::_tag( array_merge( func_get_args(), [ self::RETURN ] ) ) . $lEndOfLine;
                    break;
            }
        }
    }

    public static function displayHeader() {
        self::setHeader( self::DISPLAY );
    }

    private static function _singleRow( $pParam = null ) {
        if( !$pParam ) return;
        $lParams = explode( "|", $pParam );

        return ( self::_tag( $lParams ) );
    }

    public static function addComment( $pText ) { self::write( "<!-- " . $pText . " -->" ); }
    public static function write( $pText ) { print ( str_repeat( self::INDENT, count( self::$_stack ) ) . $pText . "\n" ); }

    public static function message( $lText ) { self::$_jsMsg .= $lText; }
}