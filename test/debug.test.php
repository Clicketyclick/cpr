<?php
/**
 * @package     ./test/debug.test.php
 *
 * Unit test for ./lib/debug.php
 *
 * 
 * @todo 
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 * @author      Erik Bachmann <ErikBachmann@ClicketyClick.dk>
 * @link        http://pear.php.net/package/PackageName
 * @deprecated  no
 * @since       2020-03-25T14:37:55
 * @version     2020-03-25T14:37:59
 */ 

$testingModule  = './lib/debug.php';
$testSum        = 16;

include_once "./lib/TestMore.php"; 
include_once "./lib/basic.io.php"; 

getTestHeader( __FILE__, $testingModule );

diag("Test planning");
plan( $testSum );

//---------------------------------------------------------------------

$GLOBALS['config']['_DEBUG_']   = TRUE; // Debugging
$GLOBALS['config']['_VERBOSE_'] = TRUE; // Trace logging
include_ok( $testingModule );

//---------------------------------------------------------------------

// Generic variables for testing
$testtimer  = array();

// Create buffer
getBuffer('STDERR'); 

printf( STDERR, "What");

echo "[".getBuffer('STDERR'). "]";
exit;




/*
debug( $msg );
trace( $msg );
verbose( $msg );

*/


/*
$GLOBALS['config']['_DEBUG_']   = FALSE;    // No debugging
$GLOBALS['config']['_VERBOSE_'] = FALSE;    // No trace logging
unset( debug() );
include_ok( $testingModule );
*/

//---------------------------------------------------------------------
$testname   = 'debug()';
note( $testname . ' - active');

$msg = 'This is debug';
note( "msg=[$msg]");
$expected   = "$msg";

test_start( $testtimer, $testname );
$result = debug( $msg );
test_duration( $testtimer, $testname );

//is( getBuffer('STDERR'), $expected, "debug()"); 

fprintf( STDERR, "hello world");
echo "buffer:[" . getBuffer('STDERR') . "]\n";


/*
// http://dk2.php.net/manual/en/wrappers.php
$GLOBALS['verbose'] = TRUE;
$GLOBALS['debug']   = TRUE;
$GLOBALS['trace']   = TRUE; 

*/


test_result( $testtimer ); // , TRUE 

//---------------------------------------------------------------------

function getBuffer( $name='STDOUT' ) {
    $buffer = "";
    //echo "getB\n";
    //echo $GLOBALS[$name];
    if ( isset($GLOBALS[$name]) ) {
        //echo "rew $name\n";
        rewind( $GLOBALS[$name] ); 
        $buffer = stream_get_contents( $GLOBALS[$name] );
        //echo "clo\n";
        fclose($GLOBALS[$name]);
    }
    //echo "remop";
    $GLOBALS[$name] = fopen('php://memory', 'r+'); 
    //echo "ret\n";
    return( $buffer );
}

?>
