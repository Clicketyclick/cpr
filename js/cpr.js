/**
 *  @file script.js
 *  @brief Testing syntax of Danish Social Security Numbers (CPR))
 *  
 *  @details    Please refere to README file at https://github.com/Clicketyclick/cpr
 *  Please note tha errors and warnings will be written to console.log. Only valid date 
 *  
 *  CPR method: https://github.com/Clicketyclick/cpr
 *  JS method:  https://codepen.io/netsi1964/pen/MaJvaW
 */

//https://stackoverflow.com/a/43718864/7485823
var sprintf = (str, ...argv) => !argv.length ? str : 
    sprintf(str = str.replace(sprintf.token||"$", argv.shift()), ...argv);

var cpr_error   = false;
var cpr         = false;

/**
 *  @fn         validate_cpr_number
 *  @brief      Validate syntax of CPR
 *  
 *  @details    Entry function for validating a CPR syntax etc.
 *  
 *  @param [in] $cpr    String to test for valid 10 digit CPR
 *  @return             0 on match OR on error - any other value
 *  
 *  @example    function checkCPR() {
 *      cpr.classList.remove("invalid");
 *      cpr.classList.remove("valid");;(validate_cpr_number(cpr.value)) ? cpr.classList.add("valid"): (cpr.value!=="") ? cpr.classList.add("invalid") : "";
 *  }    // checkCPR()
 *  
 *  var cpr = document.querySelector(".cpr");
 *  cpr.onchange = checkCPR;
 *  cpr.onkeyup = checkCPR;
 *  
 *  @todo       
 *  @bug        
 *  @warning    
 *  
 *  @see        
 *  @since      2022-10-24T09:00:51
 */
function validate_cpr_number($cpr) {
        var original = $cpr;
        $cpr = $cpr.replace(/[ \D]/ig, "");    // Remove blanks
        if ( typeof $cpr === "undefined" || $cpr === "" ) {
            console.log("cpr empty");
            return false;
        }

        if (typeof $cpr === "undefined" || $cpr === "" || $cpr.length!==10) {
            console.log("cpr not 10 digits");
            return false;
        };

        if (original !== $cpr) {
            cpr.value = $cpr;
        }

        var thisRegex = new RegExp('[0-9]{6}[-]{0,1}[0-9]{4}');

        if(!thisRegex.test( $cpr )){
            console.log("Illegal pattern: ["+$cpr+"]");
            return false;
        }

        $year           = Number( $cpr.substring( 4,6 ) );
        $month          = $cpr.substring( 2,4 );
        $day            = $cpr.substring(  0,2 );
        $serial         = $cpr.substring(  6 );
        $fullyear       = 1900 + Number( $year );
        $shortdate      = $cpr.substring( 0, 6 ); // DDMMMYY
        $serialrange    = $cpr.substring( 6, 7 );

        console.log( "Full year : "     + $fullyear );
        console.log( "Short date : "    + $shortdate );
        console.log( "Serial : "        + $serial );
        console.log( "Serial range: "   + $serialrange);


    // Check serial range to generate full year (4 digits)
    switch ( $serialrange ) {
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
            console.log( "YEAR: $year" );
            if ( 57 < $year ) {  // 1858 - 1899
                $fullyear   -= 100;
            } else {
                if ( 57 > $year )   // 2000 - 2057
                $fullyear   += 100;
            }
        break;
        case ('9'):
            if ( 36 > $year )   // 1858 - 1899
                $fullyear   += 100;
        break;
        default:
            console.log( "Illegal serial number ["+$serial+"]");  // Just in case
            return false;
    }
    //console.log( "Full year ["+$fullyear+"]" );
    
    // Validate year
    if ( 1858 > $fullyear || 2057 < $fullyear ) {
        console.log( "Illegal year ["+$fullyear+"]. Valid range is 1857-2057");
        return false;
    }
    // Validate month of year
    if ( ! ( 0 < $month && $month < 13 ) ) {    // Fixed range (at least 1)
        console.log( "Illegal month ["+$month+"]. Valid 1-12");
        return false;
    }
    //console.log( "month ["+$month+"]" );

    // Validate day of month
    $repDate    = validateDay ( $fullyear, $month, $day );
    if ( ! $repDate  ) {
        console.log( "Day not valid: "+$fullyear+"-"+$month+"-"+$day );
        return false;
    } else {    // Day or replacement day
        $day    = $repDate;
    }

    // Factor 11 check on last digit
    if ( ! validateCprModulus11( $cpr ) ) {
        console.log( "Factor 11 failed");
        return false;
    }    
    //return $main_int % 11 == 0;
    return true;
}

// Validate day
function validateDay ( $fullyear, $month, $day ) {
    $maxDays = days_in_month( $fullyear, $month );
    if ( ! ( 0 < $day && $day <= $maxDays ) ) {
        if ( 60 < $day && $day <= 60 + $maxDays  ) {    // Only replacement days are valid
           
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
                console.log( "Serial number is invalid ["+$serial+"]. Invalid ranges are 5037-5057, 6037-6057, 7037-7057, 8037-8057" );
            } else {
                console.log( sprintf("This is a replacement for [$ $ $]", $day - 60, $month, $year) );
                return $day - 60;
            }
        } else {
            console.log( "Illegal day ["+$day+"]. Valid 1-$maxDays in month $month of the year ["+$fullyear+"]" );
        }
        return false;
    }
    console.log( "day ["+$day+"] shortdate ["+$shortdate+"] full["+$fullyear+"]" );

    return $day;
}    // validateDay()




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
    $factors = [ 4, 3, 2, 7, 6, 5, 4, 3, 2, 1 ];

    for (var ciffer = 0; ciffer < $cpr.length; ciffer++) {
        $main_int += parseInt($cpr.substr(ciffer, 1)) * $factors[ciffer];
    }

    if ( $main_int % 11 != 0 ) {
        console.log( "Factor 11 failed");
        return false;
    }

    return true;;
}   // validateCprModulus11()

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


function checkCPR() {
    cpr.classList.remove("invalid");
    cpr.classList.remove("valid");;(validate_cpr_number(cpr.value)) ? cpr.classList.add("valid"): (cpr.value!=="") ? cpr.classList.add("invalid") : "";
}    // checkCPR()

function initCPR() {
    cpr = document.querySelector(".cpr");
    cpr.onchange = checkCPR;
    cpr.onkeyup = checkCPR;
}    // initCPR()
