<?php
/**
 *  @file  cpr.php
 *  @brief Validating Danish CPR-numbers
 *  
 *  @details   Syntax check and validation
 *  
 *  Functions:
 *      validateCpr         - Validate danish CPR-number
 *      days_in_month       - Calculate number of days in a month
 *  
 *  @copyright  http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 *  @author     Erik Bachmann <ErikBachmann@ClicketyClick.dk>
 *  @since      2020-04-01T16:54:17
 *  @version    2020-04-03T18:02:50
 */
include_once "lib/debug.php";


/**
 *  @fn         validateCpr
 *  @brief      Validate danish CPR-number
 *  
 *  @details    Syntax and validity checks:
 *  Syntax: (DDMMYY-LLLm or DDMMYYLLLm)
 *  Where 
 *      DD  = day (2 digits)
 *      MM  = month (2 digits)
 *      YY  = year (2 digits)
 *      LLL = Serial number, m = modulus 11.
 *  Serial number (incl. check digit) 
 *  
 *  Ranges to add century to short year:
 *  
 *  |Day        |Month      |Year       |Serial number  |Range|
 *  |---|---|---|---|---|
 *  |pos. 1-2   |pos. 3–4   |pos. 5-6   |pos. 7-10      |Range      |
 *  |01 - 31    |01 - 12    |00 - 99    |0001 - 0999    |1900 - 1999|
 *  |01 - 31    |01 - 12    |00 - 99    |1000 - 1999    |1900 - 1999|
 *  |01 - 31    |01 - 12    |00 - 99    |2000 - 2999    |1900 - 1999|
 *  |01 - 31    |01 - 12    |00 - 99    |3000 - 3999    |1900 - 1999|
 *  |01 - 31    |01 - 12    |00 - 36    |4000 - 4999    |2000 - 2036|
 *  |01 - 31    |01 - 12    |37 - 99    |4000 - 4999    |1937 - 1999|
 *  |01 - 31    |01 - 12    |00 - 57    |5000 - 5999    |2000 - 2057|
 *  |01 - 31    |01 - 12    |58 - 99    |5000 - 5999    |1858 - 1899|
 *  |01 - 31    |01 - 12    |00 - 57    |6000 - 6999    |2000 - 2057|
 *  |01 - 31    |01 - 12    |58 - 99    |6000 - 6999    |1858 - 1899|
 *  |01 - 31    |01 - 12    |00 - 57    |7000 - 7999    |2000 - 2057|
 *  |01 - 31    |01 - 12    |58 - 99    |7000 - 7999    |1858 - 1899|
 *  |01 - 31    |01 - 12    |00 - 57    |8000 - 8999    |2000 - 2057|
 *  |01 - 31    |01 - 12    |58 - 99    |8000 - 8999    |1858 - 1899|
 *  |01 - 31    |01 - 12    |00 - 36    |9000 - 9999    |2000 - 2036|
 *  |01 - 31    |01 - 12    |37 - 99    |9000 - 9999    |1937 - 1999|
 *  
 *  Valid range of years: 1858 - 2057
 *  
 *  This function checks the number of day in the particular month 
 *  for comparison
 *  
 *  Or for short:
 *  "Hell hath no fury like a woman scorned"
 *      https://en.wikipedia.org/wiki/The_Mourning_Bride
 *  
 *  @param [in] $cpr   CPR number for validation (DDMMYY-LLLm or DDMMYYLLLm)
 *  @return Return description
 *  
 *  @example
 *      $errors = validateCpr( $cpr );
 *      if (!$errors) {
 *          echo 'No errors!';
 *      } else {
 *          trigger_error(
 *              trim( 
 *                  var_export( implode(".\n\t", (array) $errors), TRUE )
 *                  , "'" 
 *          ), E_USER_WARNING );
 *      }
 *
 *  @todo       
 *  @bug        
 *  @warning    
 *  
 *  @see        https://da.wikipedia.org/wiki/CPR-nummer
 *  @see        https://en.wikipedia.org/wiki/Personal_identification_number_(Denmark)
 *  @see        https://gist.github.com/jycr753/1179d7ff5c0b4bd04c8a
 *  @see        https://da.wikipedia.org/wiki/Modulus_11
 *  @since      2020-04-01T14:28:47
 */
function validateCpr( $cpr ) {
    $mod11execptions = [ // Special dates wo modulus 11
        '010160',	// January 1st 1960
        '010164',	// January 1st 1964
        '010165',	// January 1st 1965
        '010166',	// January 1st 1966
        '010169',	// January 1st 1969
        '010170',	// January 1st 1970
        '010174',	// January 1st 1974
        '010180',	// January 1st 1980
        '010182',	// January 1st 1982
        '010184',	// January 1st 1984
        '010185',	// January 1st 1985
        '010186',	// January 1st 1986
        '010187',	// January 1st 1987
        '010188',	// January 1st 1988
        '010189',	// January 1st 1989
        '010190',	// January 1st 1990
        '010191',	// January 1st 1991
        '010192',	// January 1st 1992
    ];

    $errors = array();
    if ( empty( $cpr ) ) {
        $errors[] = 'Empty CPR value';
        return $errors;
    }
    
    // Check syntax. 10 digits and one optional '-' at pos 7
    if ( ! preg_match('/^[0-9]{6}[-]{0,1}[0-9]{4}$/', $cpr ) ) {
        $errors[] = "Illegal pattern: [$cpr]";
        array_push( $errors, "Valid patterns are: 999999-9999 or 9999999999");
        return( $errors );
    }
    $cpr    = preg_replace('/-/', '', $cpr ); // Remove '-'
    
    if ( ! preg_match( '/^[0-9]{10}$/', $cpr ) ) {
        $errors[] = 'CPR must contain 10 digits wo. separators';
        return $errors;
    }

    $year   = (int) substr( $cpr, 4,2 );
    $month  = (int) substr( $cpr, 2,2 );
    $day    = (int) substr( $cpr, 0,2 );
    $serial = (int) substr( $cpr, 7 );
    $fullyear       = 1900 + $year ;
    $shortdate      = substr( $cpr, 0, 6 ); // DDMMMYY
    $serialrange    = substr($cpr, 6,1 );

    // Check serial range to generate full year (4 digits)
    debug( "Serial range [$serialrange]");
    switch ( $serialrange ) {
        case ('5'): // 1800 - 1899
            $fullyear   -= 100;
        break;
        case ('0'):
        case ('1'):
        case ('2'):
        case ('3'): // 1900 - 1999
            //$fullyear   =1900 + $year ;
        break;
        case ('4'):
            if ( 36 < $year )   // 2000 - 2036
                $fullyear   += 100;
        break;
        case ('5'):
        case ('6'):
        case ('7'):
        case ('8'):
            if ( 57 < $year )   // 1858 - 1899
                $fullyear   -= 100;
        break;
        case ('9'):
            if ( 36 > $year )   // 1858 - 1899
                $fullyear   += 100;
        break;
        default:
            $errors[] = "Illegal serial number [$serial]";  // Just in case
            return( $errors );
    }
    debug( "Full year [$fullyear]");
    
    // Validate month
    if ( ! ( 0 < $month && $month < 13 ) ) {    // Fixed range (at least1)
        $errors[] = "Illegal month [$month]";
        array_push( $errors, "Valid 1-12");
        return( $errors );
    }
    debug( "month [$month]" );
    
    // Validate day
    $maxDays = days_in_month($month, $fullyear);
    if ( ! ( 0 < $day && $day <= $maxDays ) ) {
        $errors[] = "Illegal day [$day]";
        array_push( $errors, "Valid 1-$maxDays in month $month of the year ${fullyear}" );
        return( $errors );
    }
    debug( "day [$day]" );

    // Validate modulus 11
    if ( ! ( in_array($shortdate, $mod11execptions) ) // Date in exception list
        && 100 > ( $fullyear - 1900) ) {    // And full year between 1900 - 1999
        $main_int = 0;
        $x = 0;
        $factors = [ 4, 3, 2, 7, 6, 5, 4, 3, 2, 1 ];
        foreach ( str_split( $cpr ) as $digit ) {
            $main_int += $digit * $factors[ $x ];
            $x++;
        }
        debug( "main_int[$main_int] ". ($main_int %11)."]\n");
        debug( "main_int[$main_int] ". ($main_int %11)."]\n");

        $mod11  = $main_int % 11;
        if ( 0 != $mod11 )
            $errors[] = "Modulus11 check failed [$mod11] Expected [{$cpr[9]}]";
    }

    return( $errors );
}   // validateCpr()


/**
 *  @fn         days_in_month
 *  @brief      Calculate number of days in a month
 *  
 *  @details    Returns the number of days in a given month and year, 
 *  taking into account leap years.
 *  
 *  
 *  dbindel at austin dot rr dot com
 *  corrected by ben at sparkyb dot net
 *  
 *  @param [in] $month   numeric month (integers 1-12)
 *  @param [in] $year    numeric year ("any" integer)
 *  @return     Number of day (integer)
 *  
 *  @example    days_in_month( $month );
 *  @example    days_in_month( $year );
 *  
 *  @todo       
 *  @bug        
 *  @warning    
 *  
 *  @see        https://www.php.net/manual/en/function.cal-days-in-month.php#38666
 *  @since      2020-04-03T16:01:03
 */
function days_in_month($month, $year) {
    return(
        2 == $month
        ?   ( $year % 4 
                ? 28 
                :   ( $year % 100 
                        ?   29 
                        :   ( $year % 400 
                                ?   28 
                                :   29
                            )
                    )
            )
        :   ( ($month - 1) % 7 % 2 
                ?   30
                :   31
            )
    );
}   // days_in_month()

?>
