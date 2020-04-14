<?php
/**
 *  @file  cpr.php
 *  @brief Checking danish CPR-numbers
 *  
 *  @details   Checking syntax and validating CPR-numbers are VERY complicated.
 *      These functions are build using the descriptions and guide lines from official sources.
 *  
 *  Functions:
 *      validateCpr             - Validate danish CPR-number
 *      validateCprModulus11    - Validate that the 10th digit matches modulus 11
 *      calcCprModulus11        - Calculate modulus 11 check digit from first 9 digits
 *      days_in_month           - Calculate number of days in a month
 *  
 *  @copyright  http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 *  @author     Erik Bachmann <ErikBachmann@ClicketyClick.dk>
 *  @since      2020-04-01T16:54:17
 *  @version    2020-04-14T22:06:52
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
 *  For replacement numbers (day from 60-91)
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
 *  |01 - 31    |01 - 12    |00 - 36    |5000 - 5999    |2000 - 2036|
 *  |01 - 31    |01 - 12    |37 - 57    |5000 - 5999    |**Invalid**|
 *  |01 - 31    |01 - 12    |58 - 99    |5000 - 5999    |1858 - 1899|
 *  |01 - 31    |01 - 12    |00 - 36    |6000 - 6999    |2000 - 2036|
 *  |01 - 31    |01 - 12    |37 - 57    |6000 - 6999    |**Invalid**|
 *  |01 - 31    |01 - 12    |58 - 99    |6000 - 6999    |1858 - 1899|
 *  |01 - 31    |01 - 12    |00 - 36    |7000 - 7999    |2000 - 2057|
 *  |01 - 31    |01 - 12    |37 - 57    |7000 - 7999    |**Invalid**|
 *  |01 - 31    |01 - 12    |58 - 99    |7000 - 7999    |1858 - 1899|
 *  |01 - 31    |01 - 12    |00 - 36    |8000 - 8999    |2000 - 2057|
 *  |01 - 31    |01 - 12    |37 - 57    |8000 - 8999    |**Invalid**|
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
 *      // Get date of birth in ISO format: YYYY-MM-DD
 *      $errors = validateCpr( $cpr, TRUE );
 *      if ( isset($errors['dateofbirth']) ) {
 *          echo 'OK ['.$errors['dateofbirth'].']';
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
 *  @see        https://da.wikipedia.org/wiki/Modulus_11
 *  @see        https://cpr.dk/cpr-systemet/erstatningspersonnummer-i-eksterne-systemer/
 *  @since      2020-04-01T14:28:47
 */
function validateCpr( $cpr, $dateofbirth = FALSE ) {
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

    $errors = array();  // https://stackoverflow.com/a/7708630
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
    $serial = (int) substr( $cpr, 6 );
    $fullyear       = 1900 + $year ;
    $shortdate      = substr( $cpr, 0, 6 ); // DDMMMYY
    $serialrange    = substr($cpr, 6,1 );

    // Check serial range to generate full year (4 digits)
    debug( "Serial range [$serialrange]");
    switch ( $serialrange ) {
        //$fullyear   = 1900 + $year (Default)
        case ('0'):
        case ('1'):
        case ('2'):
        case ('3'): // 1900 - 1999: Default
        break;
        case ('4'):
            if ( 37 > $year )   // 2000 - 2036
                $fullyear   += 100;
        break;
        case ('5'):
        case ('6'):
        case ('7'):
        case ('8'):
        debug( "YEAR: $year" );
            if ( 57 < $year ) {  // 1858 - 1899
                $fullyear   -= 100;
            } elseif ( 57 > $year )   // 2000 - 2057
                $fullyear   += 100;
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

    if ( 1858 > $fullyear || 2057 < $fullyear ) {
            $errors[] = "Illegal year [$fullyear]";
            array_push( $errors, "Valid range is 1857-2057");
            return( $errors );
    }
    
    // Validate month
    if ( ! ( 0 < $month && $month < 13 ) ) {    // Fixed range (at least1)
        $errors[] = "Illegal month [$month]";
        array_push( $errors, "Valid 1-12");
        return( $errors );
    }
    debug( "month [$month]" );

    // Validate day
    $maxDays = days_in_month( $fullyear, $month );
    if ( ! ( 0 < $day && $day <= $maxDays ) ) {
        if ( 60 < $day && $day <= 60 + $maxDays  ) {
            $errors[] = "Replacement CPR [$shortdate]";
            
            // Year 37 - 57: section 5, 6, 7, 8
            if (
                ( 5037 <= $serial && 5058 > $serial ) 
                ||
                ( 6037 <= $serial && 6058 > $serial ) 
                ||
                ( 7037 <= $serial && 7058 > $serial ) 
                ||
                ( 8037 <= $serial && 8058 > $serial ) 
            ) {
                array_push( $errors, sprintf("Serial number is invalid [%s]", $serial ) );
                array_push( $errors, "Invalid ranges are 5037-5057, 6037-6057, 7037-7057, 8037-8057" );
            } else {
                array_push( $errors, sprintf("This is a replacement for [%02.2s%02.2s%02.2s]", $day - 60, $month, $year) );
            }
        } else {
            $errors[] = "Illegal day [$day]";
            array_push( $errors, "Valid 1-$maxDays in month $month of the year ${fullyear}" );
        }
        return( $errors );
    }
    debug( "day [$day]" );
    debug(  "shortdate [$shortdate] full[$fullyear]" );

    // Validate modulus 11
    if ( ! ( in_array($shortdate, $mod11execptions) ) ) {   // Date not in exception list
        $mod11  = validateCprModulus11( $cpr );
        if ( 0 != $mod11 )
            $errors[] = "Modulus11 check failed [$mod11] Expected [{$cpr[9]}]";
    }

    if ( $dateofbirth && !$errors )
        $errors = [
            'dateofbirth' => sprintf( "%04.4s-%02.2s-%02.2s", $fullyear, $month, $day )
        ];

    return( $errors );
}   // validateCpr()


/**
 *  @fn         validateCprModulus11
 *  @brief      Validate that the 10th digit matches modulus 11
 *  
 *  @details    Detailed description
 *  
 *  @param [in] $cpr    Valid 10 digit CPR
 *  @return             0 on match OR on error - any other value
 *  
 *  @example    
 *      if ( 0 == validateCprModulus11( $cpr ) ) {
 *          echo "OK";
 *      } else {
 *          echo "Not a valid modulus 11 check";
 *      }
 *  
 *  @todo       
 *  @bug        
 *  @warning    
 *  
 *  @see        
 *  @since      2020-04-14T16:08:32
 */
function validateCprModulus11( $cpr ) {
    $main_int = 0;
    $x = 0;
    $factors = [ 4, 3, 2, 7, 6, 5, 4, 3, 2, 1 ];
    foreach ( str_split( $cpr ) as $digit ) {
        $main_int += $digit * $factors[ $x ];
        $x++;
    }
    debug( "main_int[$main_int] ". ( $main_int % 11 )."]\n");

    return($main_int  % 11);
}   // validateCprModulus11()


/**
 *  @fn         calcCprModulus11
 *  @brief      Calculate modulus 11 check digit from first 9 digits
 *  
 *  @details    Caculates the 10th digit in a CPR.
 *  
 *  @param [in] $cpr    Valid 10 digit CPR or first 9 digits
 *  @return             Modulus 11 value OR -1 on error
 *  
 *  @example    calcCprModulus11( $cpr );
 *  
 *  @todo       
 *  @bug        
 *  @warning    This function does NOT perform any syntax check! It's a simple calculation
 *  
 *  @see        
 *  @since      2020-04-14T15:53:00
 */
function calcCprModulus11( $cpr ) {
    if ( 10 == strlen( $cpr ))
        $cpr    = substr($cpr,0, -1);
    if ( 9 != strlen( $cpr ))
        return( -1 );

    $main_int = 0;
    $x = 0;
    debug( "cpr [$cpr]" );
    $factors = [ 4, 3, 2, 7, 6, 5, 4, 3, 2, 1 ];
    foreach ( str_split( $cpr ) as $digit ) {
        $main_int += $digit * $factors[ $x ];
        $x++;
    }
    debug( "main_int[$main_int] ". ($main_int % 11)."]\n");
    $main_int %= 11;

    return( 11 - $main_int);
}   // calcCprModulus11()


/**
 *  @fn         days_in_month
 *  @brief      Calculate number of days in a month
 *  
 *  @details    Returns the number of days in a given month and year, 
 *  taking into account leap years.
 *  
 *  Over a period of four centuries, the accumulated error of adding a leap day 
 *  every four years amounts to about three extra days. The Gregorian calendar 
 *  therefore drops three leap days every 400 years, which is the length of 
 *  its leap cycle. 
 *  This is done by dropping February 29 in the three century years (multiples of 100) 
 *  that cannot be exactly divided by 400.[6][7] The years 1600, 2000 and 2400 
 *  are leap years, while 1700, 1800, 1900, 2100, 2200 and 2300 are not leap years. 
 *  By this rule, the average number of days per year is 
 *  365 + ​1⁄4 − ​1⁄100 + ​1⁄400 = 365.2425. 
 *  The rule can be applied to years before the Gregorian reform 
 *  (the proleptic Gregorian calendar), if astronomical year numbering is used.
 *  
 *  dbindel at austin dot rr dot com
 *  corrected by ben at sparkyb dot net
 *  Parameters swopped - Most significant (year) first
 *  
 *  @param [in] $year    numeric year ("any" integer)
 *  @param [in] $month   numeric month (integers 1-12)
 *  @return     Number of day (integer)
 *  

 *  @example    $days   = days_in_month( $year, $month );
 *  
 *  @todo       
 *  @bug        
 *  @warning    
 *  
 *  @see        https://www.php.net/manual/en/function.cal-days-in-month.php#38666
 *  @since      2020-04-03T16:01:03
 */
function days_in_month( $year, $month ) {
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
