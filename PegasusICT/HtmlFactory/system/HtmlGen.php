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


    private static $_stack = [];
    private static $_jsMsg;
    private const COPYRIGHT = 'Copyright © Mattijs Snepvangers ~ Pegasus ICT Dienstverlening  2019-';








    public static function style( string $pSrc=null, string $pCode=null, bool $pReturn=false ) {
        if(empty($pSrc.$pCode) || (!empty($pSrc)&&!empty($pCode))) return "ERROR: style() -  either src or code must be specified!";

        $format = ( !empty( $pSrc ) ) ? '<%1$s %2$s %3$s></%1$s>' : '<%1$s %2$s>%4$s</%1$s>';
        $pSrc   = ( !empty( $pSrc ) ) ? sprintf('src="%s"',$pSrc) : null;

        $lResult = sprintf( $format, ["style", "type=text/css", $pSrc, $pCode ]);


        if($pReturn) return $lResult; else print $lResult;
    }

    public static function startBlock() {
        self::$_stack[ count( self::$_stack ) ] = "stpt";
    }
    public static function endBlock() {
        for( $lIndex = count( self::$_stack )-1; $lIndex > 0; --$lIndex ) if( self::$_stack[ $lIndex ] === "stpt") break;
        if( !$lIndex ) return;
        $lClosing = count( self::$_stack ) - $lIndex - 1;
        if( (bool)$lClosing ) self::closeTag( $lClosing );
        unset ( self::$_stack[ ( count( self::$_stack ) - 1 ) ] );
    }



    public static function displayHeader() {
        self::setHeader("display");
    }

    private static function _singleRow( $pParam = null ) {
        if( !$pParam ) return;
        $lParams = explode( "|", $pParam );

        return ( self::_tag( $lParams ) );
    }

    public static function addComment( $pText ) { self::write( "<!-- " . $pText . " -->" ); }
    public static function write( $pText ) { print (str_repeat("  ", count(self::$_stack ) ) . $pText . "\n" ); }

    public static function message( $lText ) { self::$_jsMsg .= $lText; }
}