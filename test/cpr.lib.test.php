<?php
/**
 * @package     ./test/cpr.test.php
 *
 * Unit test for ./lib/cpr.php
 *
 * Requires: "./lib/TestMore.php"
 * 
 * @todo 
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 * @author      Erik Bachmann <ErikBachmann@ClicketyClick.dk>
 * @link        http://pear.php.net/package/PackageName
 * @deprecated  no
 * @since       2020-04-03T16:48:16
 * @version     2020-04-03T18:02:37
 */ 

$testingModule  = './lib/cpr.php';
$testSum        = 15;

include_once "./lib/TestMore.php"; 

getTestHeader( __FILE__, $testingModule );

diag("Test planning");
plan( $testSum );

//---------------------------------------------------------------------

include_ok( $testingModule );

//---------------------------------------------------------------------

// Generic variables for testing
$testtimer  = array();


//---------------------------------------------------------------------
note( 'validateCpr( $cpr );');
$testset = [
//          Test                Expected
/* 02 */     ''              => ['Empty CPR value'],
/* 03 */     '   '           => ['Illegal pattern: [   ]','Valid patterns are: 999999-9999 or 9999999999'],
/* 04 */     '240495'        => ['Illegal pattern: [240495]','Valid patterns are: 999999-9999 or 9999999999'],
        // Month
/* 05 */     '1600414199'    => ['Illegal month [0]','Valid 1-12'],
/* 06 */     '1613414199'    => ['Illegal month [13]','Valid 1-12'],
        // Day
/* 07 */     '0001414199'    => ['Illegal day [0]','Valid 1-31 in month 1 of the year 2041'],
/* 08 */     '3201414199'    => ['Illegal day [32]','Valid 1-31 in month 1 of the year 2041'],
/* 09 */     '2902414199'    => ['Illegal day [29]','Valid 1-28 in month 2 of the year 2041'],
        // Leap year
/* 10 */     '2902204191'    => [],//['Modulus11 check failed [8] Expected [9]'],
/* 11 */     '2902214199'    => ['Illegal day [29]','Valid 1-28 in month 2 of the year 1921'],
        // Modulus 11
/* 12 */     '2412004199'    => ['Modulus11 check failed [5] Expected [9]'],
/* 13 */     '231045-0637'   => [], // Kim Larsen
/* 14 */     '2310450637'    => [], // Kim Larsen
/* 15 */     '231045--0637'  => ['Illegal pattern: [231045--0637]'
                                , 'Valid patterns are: 999999-9999 or 9999999999'],
];

foreach ( $testset as $cpr => $expected ) {
    test_start( $testtimer, $testname );
    $result     = validateCpr( $cpr );
    //test_duration( $testtimer, $testname );
    ok( is_deeply( $result, $expected, ""), (
        $result 
        ?   '"' .implode(". ", (array) $result) . '"'
        :   "OK"
        )
        . " = cpr( $cpr )" 
    );
}

//---------------------------------------------------------------------

test_result( $testtimer ); // , TRUE 

?>
