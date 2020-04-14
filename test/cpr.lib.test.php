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
 * @version     2020-04-14T22:06:32
 */ 

$testingModule  = './lib/cpr.php';
$testSum        = 52;

include_once "./lib/TestMore.php"; 

getTestHeader( __FILE__, $testingModule );

diag("Test planning");
plan( $testSum );

//---------------------------------------------------------------------

include_ok( $testingModule );

//---------------------------------------------------------------------

// Generic variables for testing
$testtimer  = array();


$testset = [

//          Test                Expected
	2	=> [
		'type'	=> 'validateCpr',
		'test'	=> '',
		'expected'	=> ['Empty CPR value'],
		'note'		=> "Empty string",
	],
	3	=> [
		'type'	=> 'validateCpr',
		'test'	=> '   ',
		'expected'	=> ['Illegal pattern: [   ]','Valid patterns are: 999999-9999 or 9999999999'],
		'note'		=> "Blanks",
	],
	4	=> [
		'type'	=> 'validateCpr',
		'test'	=> '240495',
		'expected'	=> ['Illegal pattern: [240495]','Valid patterns are: 999999-9999 or 9999999999'],
		'note'		=> "Short date",
	],
        // Month
	5	=> [
		'type'	=> 'validateCpr',
		'test'	=> '1600414199',
		'expected'	=> ['Illegal month [0]','Valid 1-12'],
		'note'		=> "Illegal month 0",
	],
	6	=> [
		'type'	=> 'validateCpr',
		'test'	=> '1613414199',
		'expected'	=> ['Illegal month [13]','Valid 1-12'],
		'note'		=> "Illegal month 13",
	],
        // Day
	7	=> [
		'type'	=> 'validateCpr',
		'test'	=> '0001414199',
		'expected'	=> ['Illegal day [0]','Valid 1-31 in month 1 of the year 1941'],
	],
	8	=> [
		'type'	=> 'validateCpr',
		'test'	=> '3201414199',
		'expected'	=> ['Illegal day [32]','Valid 1-31 in month 1 of the year 1941'],
	],
	9	=> [
		'type'	=> 'validateCpr',
		'test'	=> '2902414199',
		'expected'	=> ['Illegal day [29]','Valid 1-28 in month 2 of the year 1941'],
	],
        // Leap year
	10	=> [
		'type'	=> 'validateCpr',
		'test'	=> '2902204191',
		'expected'	=> [],//['Modulus11 check failed [8] Expected [9]'],
		'note'		=> "Leap year",
	],
	11	=> [
		'type'	=> 'validateCpr',
		'test'	=> '2902214199',
		'expected'	=> ['Illegal day [29]','Valid 1-28 in month 2 of the year 2021'],
		'note'		=> "Conventional year",
	],
        // Modulus 11
	12	=> [
		'type'	=> 'validateCpr',
		'test'	=> '2412004199',
		'expected'	=> ['Modulus11 check failed [5] Expected [9]'],
	],
	13	=> [
		'type'	=> 'validateCpr',
		'test'	=> '231045-0637',
		'expected'	=> [],
		'note'		=> "Kim Larsen",
	],
	14	=> [
		'type'	=> 'validateCpr',
		'test'	=> '2310450637',
		'expected'	=> [],
		'note'		=> "Kim Larsen w -",
	],
	15	=> [
		'type'	=> 'validateCpr',
		'test'	=> '231045--0637',
		'expected'	=> ['Illegal pattern: [231045--0637]', 'Valid patterns are: 999999-9999 or 9999999999'],
	],
	16	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-0637',
		'expected'	=> ['Replacement CPR [831045]', 'This is a replacement for [231045]'], // Kim Larsen Replacement
	],
	17	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-4999',
		'expected'	=> ['Replacement CPR [831045]', 'This is a replacement for [231045]'], // Replacement with valid serial
	],

    
// Replacement numbers
// 5037-5057
	18	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-5036',
		'expected'	=> ['Replacement CPR [831045]', 'This is a replacement for [231045]'], // Replacement with valid serial
	],
	19	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-5037',
		'expected'	=> ['Replacement CPR [831045]', 'Serial number is invalid [5037]', 'Invalid ranges are 5037-5057, 6037-6057, 7037-7057, 8037-8057'], // Replacement with invalid serial
	],
	20	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-5057',
		'expected'	=> ['Replacement CPR [831045]', 'Serial number is invalid [5057]', 'Invalid ranges are 5037-5057, 6037-6057, 7037-7057, 8037-8057'], // Replacement with invalid serial
	],
	21	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-5058',
		'expected'	=> ['Replacement CPR [831045]', 'This is a replacement for [231045]'], // Replacement with valid serial
	],

// 6037-6057
	22	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-6036',
		'expected'	=> ['Replacement CPR [831045]', 'This is a replacement for [231045]'], // Replacement with valid serial
	],
	23	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-6037',
		'expected'	=> ['Replacement CPR [831045]', 'Serial number is invalid [6037]', 'Invalid ranges are 5037-5057, 6037-6057, 7037-7057, 8037-8057'], // Replacement with invalid serial
	],
	24	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-6057',
		'expected'	=> ['Replacement CPR [831045]', 'Serial number is invalid [6057]', 'Invalid ranges are 5037-5057, 6037-6057, 7037-7057, 8037-8057'], // Replacement with invalid serial
	],
	25	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-6058',
		'expected'	=> ['Replacement CPR [831045]', 'This is a replacement for [231045]'], // Replacement with valid serial
	],

// 7037-7057
	26	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-7036',
		'expected'	=> ['Replacement CPR [831045]', 'This is a replacement for [231045]'], // Replacement with valid serial
	],
	27	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-7037',
		'expected'	=> ['Replacement CPR [831045]', 'Serial number is invalid [7037]', 'Invalid ranges are 5037-5057, 6037-6057, 7037-7057, 8037-8057'], // Replacement with invalid serial
	],
	28	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-7057',
		'expected'	=> ['Replacement CPR [831045]', 'Serial number is invalid [7057]', 'Invalid ranges are 5037-5057, 6037-6057, 7037-7057, 8037-8057'], // Replacement with invalid serial
	],
	29	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-7058',
		'expected'	=> ['Replacement CPR [831045]', 'This is a replacement for [231045]'], // Replacement with valid serial
	],

// 8037-8057
	30	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-8036',
		'expected'	=> ['Replacement CPR [831045]', 'This is a replacement for [231045]'], // Replacement with valid serial
	],
	31	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-8037',
		'expected'	=> ['Replacement CPR [831045]', 'Serial number is invalid [8037]', 'Invalid ranges are 5037-5057, 6037-6057, 7037-7057, 8037-8057'], // Replacement with invalid serial
	],
	32	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-8057',
		'expected'	=> ['Replacement CPR [831045]', 'Serial number is invalid [8057]', 'Invalid ranges are 5037-5057, 6037-6057, 7037-7057, 8037-8057'], // Replacement with invalid serial
	],
	33	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-8058',
		'expected'	=> ['Replacement CPR [831045]', 'This is a replacement for [231045]'], // Replacement with valid serial
	],


// Get birthdate
	34	=> [
		'type'	=> 'validateCpr',
		'test'	=> '831045-9000',
		'expected'	=> ['Replacement CPR [831045]', 'This is a replacement for [231045]'], // Replacement with valid serial
	],
	35	=> [
		'type'	=> 'validateCprTrue',
		'test'	=> '2310450637',
		'expected'	=> ['dateofbirth' => '1945-10-23'], // Kim Larsen
	],
	36	=> [
		'type'	=> 'validateCprTrue',
		'test'	=> '2310957995',
		'expected'	=> ['dateofbirth' => '1895-10-23'], // 1895
	],
	37	=> [
		'type'	=> 'validateCprTrue',
		'test'	=> '2310994998',
		'expected'	=> ['dateofbirth' => '1999-10-23'], // 1999
	],

// Validate that the 10th digit matches modulus 11

	38	=> [
		'type'	=> 'validateCprModulus11',
		'test'	=> '2310994998',
		'expected'	=> [ 0 ], // 0
		'note'		=> "Correct modulus 11",
	],
	39	=> [
		'type'	=> 'validateCprModulus11',
		'test'	=> '2310994990',
		'expected'	=> "3", // 0
		'note'		=> "Wrong modulus 11",
	],

//  Calculate modulus 11 check digit from first 9 digits' );

	40	=> [
		'type'	=> 'calcCprModulus11',
		'test'	=> '2310994998',
		'expected'	=> "8", // 0
		'note'		=> "Calculate mod11, ignoring digit",
	],
	41	=> [
		'type'	=> 'calcCprModulus11',
		'test'	=> '231099499',
		'expected'	=> "8", // 0
		'note'		=> "Calculate mod11, missing 10th digit",
	],
	42	=> [
		'type'	=> 'calcCprModulus11',
		'test'	=> '2310994990',
		'expected'	=> "8", // 0
		'note'		=> "Calculate mod11 ignoring wrong digit",
	],

// Calculate number of days in a month

	43	=> [
		'type'	=> 'days_in_month',
		'test'	=> '2020-01',
		'expected'	=> "31", // 0
		'note'		=> "31 days in January",
	],
	44	=> [
		'type'	=> 'days_in_month',
		'test'	=> '2020-02',
		'expected'	=> "29", // 0
		'note'		=> "Leap year: 29 days in February",
	],
	45	=> [
		'type'	=> 'days_in_month',
		'test'	=> '2020-05',
		'expected'	=> "31", // 0
		'note'		=> "31 days in March",
	],
	46	=> [
		'type'	=> 'days_in_month',
		'test'	=> '2020-06',
		'expected'	=> "30", // 0
		'note'		=> "30 days in June",
	],
	47	=> [
		'type'	=> 'days_in_month',
		'test'	=> '2020-07',
		'expected'	=> "31", // 0
		'note'		=> "31 days in July",
	],
	48	=> [
		'type'	=> 'days_in_month',
		'test'	=> '2020-08',
		'expected'	=> "31", // 0
		'note'		=> "31 days in August",
	],

// Hundreds variation
	49	=> [
		'type'	=> 'days_in_month',
		'test'	=> '1600-02',
		'expected'	=> "29", // 0
		'note'		=> "29 days in February, 1600 (Modulo 400=0)",
	],
	50	=> [
		'type'	=> 'days_in_month',
		'test'	=> '1700-02',
		'expected'	=> "28", // 0
		'note'		=> "28 days in February, 1700 (Modulo 400=100)",
	],
	51	=> [
		'type'	=> 'days_in_month',
		'test'	=> '1800-02',
		'expected'	=> "28", // 0
		'note'		=> "28 days in February, 1800 (Modulo 400=200)",
	],
	52	=> [
		'type'	=> 'days_in_month',
		'test'	=> '1900-02',
		'expected'	=> "28", // 0
		'note'		=> "28 days in February, 1900 (Modulo 400=300)",
	],

];


foreach( $testset as $key => $value ) {
    //echo "key[$key]\t{$value['type']}\n";
    $cpr        = $value['test'];
    $expected   = $value['expected'];
    test_start( $testtimer, $testname );
    switch( $value['type']) {
        case 'validateCpr':
            $result     = validateCpr( $cpr );
        break;
        case 'validateCprTrue':
            $result     = validateCpr( $cpr, TRUE );
        break;
        case 'validateCprModulus11':
            $result     = validateCprModulus11( $cpr  );
        break;
        case 'calcCprModulus11':
            $result     = calcCprModulus11( $cpr  );
        break;
        case 'days_in_month':
            list( $year, $month )   = explode( '-', $value['test'] );
            $result     = days_in_month( $year, $month );
        break;
        
        default:
            todo( var_export($value, TRUE) );
    }
    //test_duration( $testtimer, $testname );
    ok( is_deeply( (array) $result, (array) $expected, "" ), (
        $result 
//        ?   $value['type'] . ': "' .implode(". ", (array) $result) . '"'
        ?   $value['type'] . ': "' 
            . ( isset($result[0]) ? $result[0] 
                . (isset($result[1]) 
                    ? ". ". $result[1] : "")  
                    : implode(". ", (array) $result ) 
            ) . '"'
        :   $value['type'] . ": OK"
        )
        . " = test( $cpr )" 
        . ( isset( $value['note'] ) ?  " ". $value['note'] : ". " )  // Append note
    );

    if ( ! is_deeply( (array) $result, (array) $expected, "" ) ) {
        echo "\n>>> Result\n";
        var_export($result);
        echo "\n---- Expected\n";
        var_export($expected);
        echo "\n<<<\n";
        exit;
    }

}

//test_result( $testtimer ); // , TRUE 

?>
