<?php
/** 
 * @package  lib/phpDoc.php
 * 
 * Extract PhpDoc blocks from file
 * 
 * @todo            DUMMY 
 * 
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:36:58
 * @version         2019-05-15T13:17:43
 */
// https://stackoverflow.com/a/24187909

include_once "lib/handleStrings.php";

/** 
 * @subpackage  getPhpDoc()
 * 
 * Extract PhpDoc blocks from file
 * 
 * @todo            DUMMY 
 * @example         $phpDocs = getPhpDoc( $file );
 * 
 * @param file      File name
 * @return string   PhpDoc block
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:36:58
 */
// https://stackoverflow.com/a/24187909
function getPhpDoc( $file ) {
    $str    = file_get_contents( $file );
    $start  = '/**';
    $end    = '*/';
    
    $phpdocs    = getBetweens($str, $start,$end);
    //var_export($phpdocs); echo "\n--\n"; exit;
    
    return( $phpdocs );
}   // getPhpDoc()
 
//---------------------------------------------------------------------

/** 
 * @subpackage  parsePhpDoc()
 *
 * Extract PhpDoc blocks from file
 * 
 * @todo            
 *
 * @example $phpDocs    = getPhpDoc( $file );
 * @example $phpDoc     = parsePhpDoc( $phpDocs );
 *
 * 
 * @param docs      Array of PhpDoc blocks
 * @return string   PhpDoc block
 * @URL https://stackoverflow.com/a/24187909
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:36:58
 */
function parsePhpDoc( $docs ) {
    $pattern        = '/\s*\s*\@(\w*) (.*)/is';
    $vpattern       = '/^(?!\s*\*\s*\@)/';
    $replacement    = "  [\1] [\2]";
    $docno          = 0;
    $keys   = array();
    //echo "[$docs]\n";
    //$docs   = explode( "\n", $docs);

    $keys[$docno]['keys']   = array();
    foreach( $docs as $doc ) {
        //echo "[$doc]\n";
        //if ( "" == $doc ) continue;
        // Get keys (" * @xx")
        $docArr     = explode( "\n", $doc );
        
        // Get non-keys
        //$keys[$docno]['keys']       = preg_grep( $pattern , $docArr  ); 
        $keyset = preg_grep( $pattern , $docArr  ); 
        foreach ( $keyset as $thisKey ) {
            //echo "- [$thisKey]\n";
            // Remove prefix and $
            $thisKey    = preg_replace( '/^\s*\*\s*\@/is', '', $thisKey );
            //echo "- [$thisKey]\n";
            // Split tag and comment
            $theseKeys  = explode( ' ', $thisKey, 2 );
            //var_export( $theseKeys );exit;
            //echo "$theseKeys[0]";
            // Append array
            if ( ! isset($keys[$docno]['keys'][$theseKeys[0]]) )
                $keys[$docno]['keys'][$theseKeys[0]]    = array();
            // Append key and trimmed comment
            array_push($keys[$docno]['keys'][$theseKeys[0]] , trim( $theseKeys[1] ) ); 
            //var_export( $keys );exit;
        }
        
        // Skip blank lines
        $strs       = preg_grep( $vpattern , $docArr  ); 
        //var_export( $strs );
        // Get comments
        $strs2       = preg_grep( "/^(?!\s*\*\s*\@)/", $strs  ); 
        //var_export( $strs2 );
        // Append comments
        $keys[$docno]['data']   = implode( "\n", $strs2) ;
        //var_export( $keys );
        
        $docno++;
    }
        //var_export( $keys );
        /*
        echo "\n";
        $thisKey    = ' * @subpackage  dummy1()';
        echo "- [$thisKey]\n";
        $thisKey    = preg_replace( '/^\s*\*\s*\@/is', '', $thisKey );
        echo "- [$thisKey]\n";
        $theseKeys  = explode( ' ', $thisKey, 2 );
        var_export( $theseKeys );
        */
        
    return($keys);
/*
        exit;
    exit;
    if (preg_match_all($pattern, $str, $match) >= 1) {
        for ( $i = 0; $i < sizeof( $match[1] ) ; $i++ ) {
            $value  = $match[1][$i];
            $doc    .= preg_replace( $vpattern, $replacement, $value );
        }
    }
    return( $doc );
*/
}   // getPhpDoc()

//---------------------------------------------------------------------

/** 
 * @subpackage  getPhpDocComment()
 *
 * Concatenate comments with delimiter
 * 
 * @todo            
 * 
 * @param block     Text block
 * @param tag       identifier tag
 * @param postDelimiter suffix
 * @param preDelimter   prefix
 * @return string   PhpDoc block
 * @URL https://stackoverflow.com/a/24187909
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:36:58
 */
// 
function getPhpDocComment( $block, $tag, $postDelimiter="\n", $preDelimiter="\n" ) {
    if (! isset($block['keys'][$tag])) {
        return(FALSE);
    }
      //var_export($block['keys'][$tag]);  
      //var_export( implode( $postDelimiter, $block['keys'][$tag] ) );
    if ( is_array($block['keys'][$tag]) ) {
        return( trim( implode( $postDelimiter, $block['keys'][$tag] ) ) );
    }
            
    //var_export($block['keys']);
    //echo "tag: $tag\t{$block['keys'][$tag]}\n";
    return( trim( preg_replace( "/$preDelimiter/", $postDelimiter, $block['keys'][$tag] ) ) );
    //return( trim( preg_replace( '/\n/', $postDelimiter, $block['keys'][$tag] ) ) );
}   // getPhpDocComment()

//---------------------------------------------------------------------

function getPhpDocHeader( $file, $name="" ) {
    $d=$p=12;   // Width and precicion for tags
    // >>> Test header ----------------------------------------------------
    $header = str_repeat("-", 70) . "\n";
    // Reading and parsing phpdoc of test file !!
    $phpDocs    = getPhpDoc( $file );
    //var_export($phpDocs);
    $phpDoc     = parsePhpDoc( $phpDocs );
    // Setting top = file header
    $phpDocTop  = $phpDoc[0];

    $header .= "$name: " 
        . getPhpDocComment( $phpDocTop, "package", "\n\n" );

    // Description
    $block  = getPhpDocComment( $phpDoc[0], "data", "\n# " );
    $block  = $phpDocTop['data'];
    $block  = preg_replace( "/#\s*\*\s*/", "", $block );
    $block  = preg_replace( "/\n#/", "", $block );
    $header .= "\n$block\n";
    
    foreach ( ['todo', 'link', 'license', 'deprecated','author','since','version'] as $tag ) {
    //echo "$tag: [{$phpDocTop['keys'][$tag][0]}]\n";
        if ( isset( $phpDocTop['keys'][$tag] ) && !empty($phpDocTop['keys'][$tag][0]) ) {
            //echo getPhpDocComment( $phpDocTop, $tag, "\n#\n" ) . "..\n";
            $header .= sprintf("%-{$d}.{$p}s %s\n", ucfirst($tag) . ':', getPhpDocComment( $phpDocTop, $tag, "\n#\n" ) );
        }
    }
    
        $header .= str_repeat("-", 70)
        ;
    // <<< Test header ----------------------------------------------------
    
    return( $header );
    //return( explode( "\n", $header) );
}

//---------------------------------------------------------------------


?>
