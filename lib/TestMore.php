<?php
/**
 * @package     lib/TestMore.php
 *
 * Test framework that implements Perls Test::More as described at:
 * https://metacpan.org/pod/distribution/Test-Simple/lib/Test/More.pm
 * https://metacpan.org/pod/Test::More
 * https://metacpan.org/pod/Test2::Suite
 * http://shiflett.org/code/test-more.php
 *
 * Structures may replicate functionality found in other libraries
 * but this library is entirely based on the documentation
 * for Perls Test::More
 *
 * Run the self test: php ./test/TestMore.lib.test.php
 *
 * Please note that this module has been extended with some features,
 * that is nice to have - and not a part of TestMore
 *
 * @todo 
 * @license     
 * @author      Erik Bachmann <ErikBachmann@ClicketyClick.dk>
 * @link        
 * @deprecated  no
 * @since       2019-01-23T09:22:16
 * @version     2020-04-03T11:33:22
 */

include_once 'phpDoc.php';
register_shutdown_function('sum_up_testing');

$_no_plan       = FALSE;
$_num_failures  = 0;
$_num_skips     = 0;
$_test_num      = 0;
$_test_planed   = 0;
$_skip_all      = FALSE;
$_error_list    = array();
$_done_testing  = FALSE;
$_dump_on_error = FALSE;

// https://stackoverflow.com/a/28898174
if(!defined('STDIN'))  define('STDIN',  fopen('php://stdin',  'r'));
if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'w'));
if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'w'));


//---------------------------------------------------------------------

/**
 * @subpackage  plan()
 *
 * Configure the test plan w. no. of tests
 *
 * $example         plan('no_plan');
 * $example         plan('skip_all');
 * $example         plan(array('skip_all' => 'My reason is...'));
 * $example         plan(23);
 * @param plan      Count for numbers of test - or type like 'no_plan', 'skip_all' or an array of types
 * @return          <code>Void</code>
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @ see             
 * @since           2019-01-23T09:22:16
 */
function plan($plan) {
    global $_no_plan;
    global $_skip_all;
    global $_test_planed;
    $_no_plan       = FALSE;
    $_skip_all      = FALSE;

    switch ($plan) {
        case 'no_plan':
            $_no_plan = TRUE;
            break;
        case 'skip_all':
            fwrite( STDERR, "1..0\n");
            $_skip_all   = TRUE;
            exit(255);
            break;
        default:
            if (is_array($plan)) {
                BAIL_OUT( "1..0 # Skip {$plan['skip_all']}");
            }
            $_test_planed   = $plan;
            fwrite( STDERR, "1..$plan\n" );
            break;
    }
}   // plan()

//---------------------------------------------------------------------

/**
 * @subpackage  skip_all()
 *
 * When you want to completely skip an entire testing script.
 * 
 * $example         skip_all('My reason is...');
 * @return          <code>Void</code>
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @ see             
 * @since           2019-01-27T15:03:56
 */
function skip_all( $skip_reason ) {
    diag("Skip all because: ". $skip_reason);
    plan('skip_all');
}   // skip_all()


//---------------------------------------------------------------------

/**
 * @subpackage  done_testing()
 *
 * Post declaring the number of tests
 *
 * There are cases when you will not know beforehand how many 
 * tests your script is going to run. 
 * In this case, you can declare your tests at the end.
 *
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-27T15:09:18
 */
function done_testing( $number_of_tests_run=0 ) {
    global $_no_plan;
    global $_num_failures;
    global $_test_num;
    global $_test_planed;
    global $_error_list;
    global $_done_testing;
    $_done_testing   = TRUE;

    if ( $_no_plan ) {
        diag( array( "",
                //"The end is near! ",
                __FUNCTION__,
                "You did NOT have a plan!, But executed [$_test_num] test(s)",
                ( $_num_failures ? "Warning! Some": "Success! No") ." test(s) [$_num_failures] failed"
            )
        );
    } else {
        diag( array("", "Oops! ".__FUNCTION__."() says you were not without a plan for your $number_of_tests_run test(s)", ""));
    }
    return( $_num_failures ? FALSE : TRUE );
}   // done_testing()

//---------------------------------------------------------------------

/**
 * @subpackage  sum_up_testing()
 *
 * Summing up the tests
 * Useful to wrap up after a long test session
 *
 * NOTE! This is NOT a part of Test::More
 *
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:37:05
 */
function sum_up_testing() {
    global $_no_plan;
    global $_num_failures;
    global $_test_num;
    global $_test_planed;
    global $_error_list;
    global $_done_testing;

    diag( array(
        "",
        __FUNCTION__ . "() says:" 
    ));
    if ( ! $_done_testing ) {
        diag( array( 
            //"The end is near! ",
            ( $_no_plan ? "You did NOT have a plan! But executed [$_test_num] test(s)" : "You had a plan and executed [$_test_num]/[$_test_planed] test(s)"),
            ( $_num_failures ? "Warning! Some": "Success! No") ." tests [$_num_failures] failed"
            )
        );
    }

    if ( $_no_plan ) {
        fwrite( STDERR, "1..$_test_num\n");
    }else if ( $_test_num > $_test_planed ) {
        diag( "More tests than expected:  run=$_test_num > planned=$_test_planed");
    } else if ( $_test_num < $_test_planed ) {
        diag( "Fewer tests than expected: run=$_test_num < planned=$_test_planed");
    } else {
        diag( "Spot on! The planned number of tests were run: run=$_test_num = planned=$_test_planed");
    }

    if ($_num_failures) {
        diag("Looks like you failed $_num_failures tests of $_test_num:");
        foreach ($_error_list as $testno => $msg) {
            diag( " [$testno]: $msg");
        }
    }
    
    exit( $_num_failures ); // Return no of errors
}   // sum_up_testing()

//---------------------------------------------------------------------

/**
 * @subpackage  require_ok
 *
 * Check if module can be included
 *
 * Note! Testing with require WILL cause the entire test to halt
 * if module fails to load. Use include in test - even in require_3)
 *
 * @param module    Name (and path) to module to test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function require_ok($module)
{
    $pass = ((include $module) == 1);
    return ok($pass, "require: $module");
}   // require_ok()

//---------------------------------------------------------------------

/**
 * @subpackage  include_ok
 *
 * Check if module can be included
 *
 * Note! Testing with require WILL cause the entire test to halt
 * if module fails to load. Use include in test - even in require_ok()
 *
 * This is NOT defined in Test:More - but in Apache-test.pm
 *
 * @param module    Name (and path) to module to test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function include_ok($module)
{
    $pass = ((include $module) == 1);
    //return ok($pass);
    return ok($pass, "include: $module ");
}   // include_ok()

//---------------------------------------------------------------------

/**
 * @subpackage  ok()
 *
 * Simply evaluates any expression ($got eq $expected is just a simple example).
 * Uses that to determine if the test succeeded or failed. A true expression passes, a false one fails. Very simple.
 *
 * @example     ok( $exp{9} == 81,                   'simple exponential' );
 * @example     ok( Film->can('db_Main'),            'set_db()' );
 * @example     ok( $p->tests == 4,                  'saw tests' );
 * @example     ok( !grep(!defined $_, @items),      'all items defined' );
 *
 * @param pass      Expression to evalueate
 * @param test_name Name of test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function ok($pass, $test_name = '') {
    global $_test_num;
    global $_num_failures;
    global $_num_skips;
    global $_error_list;
    
    $_test_num++; 

    if ($_num_skips) {
        $_num_skips--;
        return TRUE;
    }
/*
    if ( is_array($test_name) && (!empty($test_name)) && ( '#' != $test_name[0]  ) ) {
        
        //$test_name = "- " . implode( " - ", $test_name);
        $test_name = "- " . var_export( $test_name, TRUE);
    }
*/

    if ( is_array($test_name) ) {
        $test_name  = var_export($test_name, TRUE);
    }
       
    if ($pass) {
        fwrite( STDERR, "ok $_test_num\t$test_name\n");
        $pass   = TRUE;
    } else {
        fwrite( STDERR, "not ok $_test_num\t$test_name\n");

        $_num_failures++;
        $caller = debug_backtrace();

        if (strstr($caller['0']['file'], $_SERVER['PHP_SELF'])) {
            $file = $caller['0']['file'];
            $line = $caller['0']['line'];
        } else {
            if( isset($caller['1']) ) {
                $file = $caller['1']['file'];
                $line = $caller['1']['line'];
            } else {
                $file = $caller['0']['file'];
                $line = $caller['0']['line'];
            }
        }

        $file = str_replace($_SERVER['DOCUMENT_ROOT'], 't', $file);
        
        $_error_list[$_test_num]    = basename( $file ) . "[$line]";
        diag( "! Failing test [{$_error_list[$_test_num]}]" );
        $pass   = FALSE;
    }

    return $pass;
}   // ok()

//---------------------------------------------------------------------

/**
 * @subpackage  is()
 *
 * Similar to ok(), is() and isnt() compare their two arguments 
 * with eq and ne respectively and use the result of that to determine 
 * if the test succeeded or failed.
 *
 * @example     is( ultimate_answer(), 42, "Meaning of Life" ); // Is the ultimate answer 42?
 * @example     isnt( $foo, '',     "Got some foo" );   // $foo isn't empty
 * 
 * @param this      First argument
 * @param that      Second argument
 * @param test_name Name of test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function is($This, $that, $test_name = '')
{
    global $_error_list;
    global $_test_num;
    global $_dump_on_error;
    
    $pass = ($This == $that);

    ok($pass, $test_name);

    if (!$pass) {
        $msg    = "        got: '$This'\n#    expected: '$that'";
        diag( $msg );
        $_error_list[$_test_num]    .= "\n# $msg";
        if ( $_dump_on_error )
            echo dumpTestData( $This, $that );
    }

    return $pass;
}   // is()

//---------------------------------------------------------------------

/**
 * @subpackage  isnt()
 *
 * Similar to ok(), is() and isnt() compare their two arguments 
 * with eq and ne respectively and use the result of that to determine 
 * if the test succeeded or failed.
 *
 * @example     is( ultimate_answer(), 42, "Meaning of Life" ); // Is the ultimate answer 42?
 * @example     isnt( $foo, '',     "Got some foo" );   // $foo isn't empty
 * 
 * @param this      First argument
 * @param that      Second argument
 * @param test_name Name of test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function isnt($This, $that, $test_name='isnt()') {
    global $_dump_on_error;
    
    $pass = ($This != $that);

    ok($pass, $test_name);

    if (!$pass) {
        diag("    '$This'");
        diag('        !=');
        diag("    '$that'");

        if ( $_dump_on_error )
            echo dumpTestData( $This, $that );
    }

    return $pass;
}   // isnt()


//---------------------------------------------------------------------

/**
 * @subpackage  diag()
 *
 * Prints a diagnostic message which is guaranteed not to interfere with 
 * test output. 
 * Like print @diagnostic_message is simply concatenated together.
 *
 * Prints to STDERR
 *
 * @param message   The diagnostic message (default empty)
 * @return          <code>FALSE</code> to preserve failure
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function diag( $message = "" ) {
    if (is_array($message)) {
        foreach($message as $current) {
            fwrite( STDERR, "# $current\n");
        }
    } else {
        fwrite( STDERR, "# $message\n");
    }
    return( FALSE );
}   // diag()

//---------------------------------------------------------------------

/**
 * @subpackage  note()
 *
 * Prints a diagnostic message which is guaranteed not to interfere with 
 * test output. 
 * Like print @diagnostic_message is simply concatenated together.
 *
 * Like diag() , except the message will not be seen when the test is 
 * run in a harness. It will only be visible in the verbose TAP stream.
 *
 * Handy for putting in notes which might be useful for debugging, 
 * but don't indicate a problem.
 * 
 * Prints to STDOUT
 *
 * @param message   The message
 * @return          <code>FALSE</code> to preserve failure.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function note( $message = "" ) {
    if (is_array($message)) {
        foreach($message as $current) {
            fwrite( STDOUT, "# $current\n");
        }
    } else {
        fwrite( STDOUT, "# $message\n");
    }
    return( FALSE );
}   // note()

//---------------------------------------------------------------------

/**
 * @subpackage  explain()
 *
 * Will dump the contents of any references in a 
 * human readable format (= var_export( , TRUE); ). 
 * Usually you want to pass this into note or diag.
 *
 * @param message   Some data to dump
 * @return          string of the data
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-27T15:32:36
 */
function explain( &$message ) {
    return( var_export( $message, TRUE) );
}   // explain()

//---------------------------------------------------------------------

/**
 * @subpackage  like()
 *
 * Similar to ok(), like() matches $got against the regex qr/expected/.
 * 
 * @example     like($got, qr/expected/, 'this is like that');
 * @example     ok( $got =~ m/expected/, 'this is like that');
 * 
 * @param string    String to search
 * @param pattern   Search pattern
 * @param test_name Name of test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 *
 */
function like($string, $pattern, $test_name = '') {
    $pass = preg_match($pattern, $string);

    ok($pass, $test_name);

    if (!$pass) {
        diag("                  '$string'");
        diag("    doesn't match '$pattern'");
    }

    return $pass;
}   // like()

//---------------------------------------------------------------------

/**
 * @subpackage  unlike()
 *
 * Works exactly as like(), only it checks if $got does not match the given pattern
 *
 * @param string    String to search
 * @param pattern   Search pattern
 * @param test_name Name of test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 *
 */
function unlike($string, $pattern, $test_name = '') {
    $pass = !preg_match($pattern, $string);

    ok($pass, $test_name);

    if (!$pass) {
        diag("                  '$string'");
        diag("          matches '$pattern'");
    }

    return $pass;
}   // unlike()

//---------------------------------------------------------------------

/**
 * @subpackage  cmp_ok()
 *
 * Halfway between ok() and is() lies cmp_ok(). 
 * This allows you to compare two arguments using any binary perl operator. 
 * The test passes if the comparison is true and fails otherwise.
 *
 * 
 * @param this      First argument
 * @param operator  Comparison string
 * @param that      Second argument
 * @param test_name Name of test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function cmp_ok($This, $operator, $that, $test_name = '') {
    global $_dump_on_error;
    
    eval("\$pass = (\$This $operator \$that);");

    ob_start();
    var_dump($This);
    $_this = trim(ob_get_clean());

    ob_start();
    var_dump($that);
    $_that = trim(ob_get_clean());

    ok($pass, $test_name);

    if (!$pass) {
        diag("         got: $_this");
        diag("    expected: $_that");
        
        if ( $_dump_on_error )
            echo dumpTestData( $_this, $_that );
    }

    return $pass;
}   // cmp_ok()

//---------------------------------------------------------------------

/**
 * @subpackage  is_deeply()
 *
 * Similar to is(), except that if $got and $expected are references, 
 * it does a deep comparison walking each data structure to see if 
 * they are equivalent. 
 * If the two structures are different, it will display the place 
 * where they start differing.
 *
 * 
 * @example         is_deeply( $got, $expected, $name );
 *
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:36:13
 * 
 * @param got       Mix complex structures. Result to test
 * @param expected  Mix complex structures. Pattern to match
 * @param name      string Name of test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-25T07:19:06
 */
function is_deeply( $got, $expected, $test_name="" ) {
    global $_dump_on_error;
    
    $a1 = array_diff_assoc_recursive($got, $expected);
    $a2 = array_diff_assoc_recursive($expected, $got);
    
    if (( 0 == $a1 ) && ( 0 == $a2)) {
        return( TRUE );
    }
    
    if ( $_dump_on_error )
        echo "a1[". var_export($a1, TRUE). "]\n";
        echo "a2[". var_export($a2, TRUE). "]\n";
        
        echo dumpTestData( var_export($got, TRUE), var_export($expected, TRUE) );

    return( FALSE );
}   // is_deeply()

//---------------------------------------------------------------------

/**
 * @subpackage  skip()
 *
 * Skip a specific number of tests for a specific reason
 *
 * @param message   Reason to skip
 * @param num       Number of tests to skip
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @todo            Find a better implementation
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function skip($message, $num) {
    global $_num_skips;

    if ($num < 0) {
        $num = 0;
    }

    for ($i = 0; $i < $num; $i++) {
        pass("# SKIP $message");
    }

    $_num_skips = $num;
}   // skip()

//---------------------------------------------------------------------

/**
 * @subpackage  todo()
 *
 * Listing tests to do - in the near future
 *
 * @example         todo( array( "is( $spoon, 'bent', "Spoon bending", ok( $exp{9} == 81, 'simple exponential' )  ) ) );
 * 
 * @param arr       Reference array of tests
 * @return          <code>TRUE</code>
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @since           2019-01-23T09:36:49
 */
function todo( ...$arr ) {
    //var_export( $arr );
    if ( 'array' != gettype($arr) ) // Fake array
        $arr    = (array) $arr;

    diag( "TODO: " . count($arr) );
    if (! is_array($arr)) {
        diag( " - todo: $arr" );
    } else {
        foreach( $arr as $test) {
            diag( " - todo: ". implode( "\n# -- todo: ", (array) $test ) );
        }
    }
    return( TRUE );
}   // todo()

/**
 * @subpackage  can_ok()
 *
 * Checks to make sure the module or object can do these methods
 * 
 * Should work with functions, too.
 *
 * @example         can_ok('Foo', qw(this that whatever));
 *
 * @param object    Object / function name
 * @param methods   List of methods
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function can_ok($object, $methods) {
    $pass = TRUE;
    $errors = array();

    foreach ($methods as $method) {
        if (!method_exists($object, $method)) {
            $pass = FALSE;
            $errors[] = "    method_exists(\$object, $method) failed";
        }
    }

    if ($pass) {
        ok(TRUE, "method_exists(\$object, ...)");
    } else {
        ok(FALSE, "method_exists(\$object, ...)");
        diag($errors);
    }

    return $pass;
}   // can_ok()

//---------------------------------------------------------------------

/**
 * @subpackage  isa_ok()
 *
 * Checks to see if the given $object->isa($class). 
 * Also checks to make sure the object was defined in the first place.
 *
 * 
 * 
 * @example         can_ok('Foo', qw(this that whatever));
 *
 * @param object    Object / function name
 * @param expected_class    Class definition
 * @param object_name       Call name
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function isa_ok($object, $expected_class, $object_name = 'The object') {
    $got_class = get_class($object);

    if (version_compare(phpversion(), '5', '>=')) {
        $pass = ($got_class == $expected_class);
    } else {
        $pass = ($got_class == strtolower($expected_class));
    }

    if ($pass) {
        ok(TRUE, "$object_name isa $expected_class");
    } else {
        ok(FALSE, "$object_name isn't a '$expected_class' it's a '$got_class'");
    }

    return $pass;
}   // isa_ok()

//---------------------------------------------------------------------

// Not implemented
//#### new_ok

/**
 *  @brief Brief description
subtest
    subtest $name => \&code, @args;
subtest() runs the &code as its own little test with its own plan and its own result. The main test counts this as a single test using the result of the whole subtest to determine if its ok or not ok.

For example...

  use Test::More tests => 3;
 
  pass("First test");
  subtest 'An example subtest' => sub {
      plan tests => 2;
      pass("This is a subtest");
      pass("So is this");
  };
  pass("Third test");
This would produce.

  1..3
  ok 1 - First test
      # Subtest: An example subtest
      1..2
      ok 1 - This is a subtest
      ok 2 - So is this
  ok 2 - An example subtest
  ok 3 - Third test
A subtest may call skip_all . No tests will be run, but the subtest is considered a skip.

  subtest 'skippy' => sub {
      plan skip_all => 'cuz I said so';
      pass('this test will never be run');
  };
Returns true if the subtest passed, false otherwise.

 *  
 *  @param [in] $test Description for $test
 *  @return Return description
 *  
 *  @details More details
 */
 

/**
 * @subpackage  subtest()
 *
 * subtest() runs the &code as its own little test with its own plan 
 * and its own result. The main test counts this as a single test 
 * using the result of the whole subtest to determine if its ok or not ok.
 *
 
 * @param test  pointer to code
 * @param args  arguments to passe to test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function subtest( $test, $args = FALSE ) {
    trigger_error( __FUNCTION__ . " is not implemented", E_USER_WARNING );
    todo( $test );
    return( FALSE );
}   //subtest()


//---------------------------------------------------------------------

/**
 * @subpackage  pass()
 *
 * Sometimes you just want to say that the tests have passed. 
 * Usually the case is you've got some complicated condition that is difficult 
 * to wedge into an ok(). In this case, you can simply use pass() 
 * (to declare the test ok) or fail (for not ok). 
 * They are synonyms for ok(1) and ok(0).
 *
 * Use these very, very, very sparingly! (Read: Don't!)
 *
 * @param test_name Name of test to pass onn
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function pass($test_name = '') {
    //$caller = debug_backtrace();
    //return ok(TRUE, $caller );
    return ok(TRUE, $test_name );
}   // pass()

//---------------------------------------------------------------------

/**
 * @subpackage  fail()
 *
 * Sometimes you just want to say that the tests have failed. 
 * Usually the case is you've got some complicated condition that is difficult 
 * to wedge into an ok(). In this case, you can simply use pass() 
 * (to declare the test ok) or fail (for not ok). 
 * They are synonyms for ok(1) and ok(0).
 *
 * Use these very, very, very sparingly! (Read: Don't!)
 *
 * @param test_name Name of test to pass onn
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:22:16
 */
function fail($test_name = '') {
    $caller = debug_backtrace();
    return( ok(FALSE, var_export( $caller, TRUE ) ) );
    //return( ok(FALSE, $test_name ) );
}   // fail()

//---------------------------------------------------------------------

/**
 * @subpackage  BAIL_OUT()
 *
 * Synonym for trigger_error(), die(), exit()
 *
 * @param module    Name (and path) to module to test
 * @return          <code>VOID</code> = exits
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-27T16:30:30
 */
function BAIL_OUT($reason) {
    debug_print_backtrace();
    trigger_error($reason, E_USER_ERROR);
}   // BAIL_OUT()

//---------------------------------------------------------------------

/**
 * @subpackage  eq_array()
 *
 * Checks if two arrays are equivalent - and in same order. 
 *
 * This is a deep check, so multi-level structures are handled too.
 * 
 * @example         $is_eq = eq_array( &got, &expected);
 * @param got       Mix complex structures. Result to test
 * @param expected  Mix complex structures. Pattern to match
 * @param name      string Name of test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:36:25
 */
function eq_array( &$got, &$expected, $test_name="eq_array" ) {
    return( is_deeply( $got, $expected, $test_name ) );
    /*
    $a1 = array_diff_assoc_recursive($got, $expected);
    $a2 = array_diff_assoc_recursive($expected, $got);
    
    if (( 0 == $a1 ) && ( 0 == $a2)) {
        return( TRUE );
    }
    return( FALSE );
    */
}   // eq_array()

//---------------------------------------------------------------------

/**
 * @subpackage  eq_hash()
 *
 * Determines if the two hashes contain the same keys and values. 
 * Similar to eq_array(), except the order of the elements is not 
 * important. 
 * This is a deep check, but the irrelevancy of order only applies to the top level.
 *
 * @example         $is_eq = eq_hash( $got, $expected);
 * @param got       Reference to mix complex structure. Result to test
 * @param expected  Reference to mix complex structure. Pattern to match
 * @param name      string Name of test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 *
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:36:34
 */
function eq_hash( &$got, &$expected, $test_name="eq_hash" ) {
    return( is_deeply( $got, $expected, $test_name ) );
    /*
    $a1 = array_diff_assoc_recursive($got, $expected);
    $a2 = array_diff_assoc_recursive($expected, $got);
    
    if (( 0 == $a1 ) && ( 0 == $a2)) {
        return( TRUE );
    }
    return( FALSE );
    */
}   // eq_hash()

//---------------------------------------------------------------------

/**
 * @subpackage  eq_set()
 *
 * Checks if two arrays are equivalent. 
 *
 * Similar to eq_array(), except the order of the elements is not important. 
 * This is a deep check, but the irrelevancy of order only applies to the top level.
 * 
 * @example         $is_eq = eq_set( $got, $expected );
 * 
 * @todo            Missing name of test
 * 
 * @param got       Reference to mix complex structure. Result to test
 * @param expected  Reference to mix complex structure. Pattern to match
 * @param test_name string Name of test
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-23T09:36:42
 */
function eq_set( &$got, &$expected, $test_name="eq_set" ) {
    return( is_deeply( $got, $expected, $test_name ) );
}   // eq_set()

//---------------------------------------------------------------------

/**
 * @subpackage  runtests()
 *
 * This runs all the given @test_files and divines 
 * whether they passed or failed based on their output 
 * to STDOUT. 
 * It prints out each individual test which failed 
 * along with a summary report and a how long it all took.
 *
 * Returns a list of two values, $total and $failed, 
 * describing the results. $total is a hash ref summary 
 * of all the tests run. Its keys and values are this:
 * 
 * <pre>
 * bonus           Number of individual todo tests unexpectedly passed
 * max             Number of individual tests ran
 * ok              Number of individual tests passed
 * sub_skipped     Number of individual tests skipped
 * todo            Number of individual todo tests
 *  
 * files           Number of test files ran
 * good            Number of test files passed
 * bad             Number of test files failed
 * tests           Number of test files originally given
 * skipped         Number of test files skipped
 * </pre>
 * 
 * If $total->{bad} == 0 and $total->{max} > 0, you've got a successful test.
 * 
 * $failed is a hash ref of all the test scripts that failed. 
 * Each key is the name of a test script, each value is 
 * another hash representing how that script failed. 
 * Its keys are these:
 * <pre>
 * name        Name of the test which failed
 * estat       Script's exit value
 * wstat       Script's wait status
 * max         Number of individual tests
 * failed      Number which failed
 * canon       List of tests which failed (as string).
 * </pre>
 * $failed should be empty if everything passed.
 *
 *
 * @param module    Name (and path) to module to test
 * @return          <code>TRUE</code> on success or <code>errorlist</code> otherwise.
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-01-27T16:27:15
 */
function runtests( &$test_files) {
    //$_error_list = array();
    //$_test_num   = 0;
    global $_error_list;
    global $_test_num;
    $status     = 0;
    $testno     = 0;
    foreach ( $test_files as $test_file ) {
        $test   = sys_get_temp_dir() . "/". basename($test_file) . ".test";
        $cmd    = "php $test_file 1>$test 2>&1";
        //echo $cmd;
        exec( $cmd, $result, $returnvalue);
        $caller = debug_backtrace();

        $file = $caller['0']['file'];
        $line = $caller['0']['line'];

        $file = str_replace($_SERVER['DOCUMENT_ROOT'], 't', $file);
        
        ok( 0 == $returnvalue, basename( $test_file ) );

        if ( $returnvalue ) {
            //if ( ! isset( $_error_list[$_test_num] ) ) $_error_list[$_test_num] = "";
            $_error_list[$_test_num]    .= ":\n#     [$test_file]";
            $status     += $returnvalue;
            echo "See [$test]\n";
            echo "return:\n[$returnvalue]\n";
        } else
            unlink( $test );
        $testno++;
    }
    return( $status ? $_error_list : TRUE );
}   // runtests()

//---------------------------------------------------------------------

/**
 * @subpackage  array_diff_assoc_recursive()
 *
 * Compare to arrays by key and value
 *
 * [NOTE BY danbrown AT php DOT net: The array_diff_assoc_recursive function is a 
 * combination of efforts from previous notes deleted.
 * Contributors included (Michael Johnson), (jochem AT iamjochem DAWT com), 
 * (sc1n AT yahoo DOT com), and (anders DOT carlsson AT mds DOT mdh DOT se).]
 *
 * This is NOT a part of Test:More, but is required for comparing structures
 *
 * @todo            Should return false on no diff?

 * @link            https://www.codeproject.com/Questions/780780/PHP-Finding-differences-in-two-multidimensional-ar
 * @example         array_diff_assoc_recursive($array1, $array2);
 * 
 * @param arr       Reference array of tests
 * @return          <code>difference</code> or <code>0</code>
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @since           2019-01-23T09:36:49
 */
function array_diff_assoc_recursive($array1, $array2) {
    foreach($array1 as $key => $value) {
        if(is_array($value)) {
            if(!isset($array2[$key])){
                $difference[$key] = $value;
            } elseif(!is_array($array2[$key])) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if($new_diff != FALSE) {
                    $difference[$key] = $new_diff;
                }
            }
        } elseif(!isset($array2[$key]) || $array2[$key] != $value ) {
            $difference[$key] = $value;
        }
    }
    return !isset($difference) ? 0 : $difference;
}   // array_diff_assoc_recursive()


//---------------------------------------------------------------------

/**
 * @subpackage  getTestHeader()
 *
 * Print test header info 
 *
 *
 * This is NOT a part of Test:More, but is used for uniform headers
 *
 * @example         getTestHeader( __FILE__, $testingModule );
 * 
 * @param file      path/name of test file
 * @param testingModule path/name of module to test
 * @return          Arrays of keys ())
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @since           2019-06-29T13:55:22
 */
function getTestHeader( $file, $testingModule = __FILE__ ) {
    
    // >>> Test header ----------------------------------------------------

    // Reading and parsing phpdoc of test file !!
    $phpDocs    = getPhpDoc( $file );

    $phpDoc     = parsePhpDoc( $phpDocs );
    // Setting top = file header
    $phpDocTop  = $phpDoc[0];

    diag( "Test:        " 
        . getPhpDocComment( $phpDocTop, "package", "\n# " ) );
    diag( "Version:     " 
        . getPhpDocComment( $phpDocTop, "version", "\n# " ) . " Since: " 
        . getPhpDocComment( $phpDocTop, "since", "\n# " ) );
    diag( "Author:      " .  getPhpDocComment( $phpDocTop, "author", "\n# " ) );
    // <<< Test header ----------------------------------------------------


    // >>> Subject header -------------------------------------------------
    diag(  );
    // Reading and parsing phpdoc of lib file !!
    $phpDocs    = getPhpDoc( $testingModule );
    $phpDoc1     = parsePhpDoc( $phpDocs );
    // Setting top = file header
    $phpDocTop  = $phpDoc1[0];

    diag( "Testing:     " 
        . getPhpDocComment( $phpDocTop, "package", "\n# " ) );

    diag( "Description: " 
        . getPhpDocComment( $phpDocTop, "data", "\n#  ", "\n \* " ) );

    diag( "Version:     " 
        . getPhpDocComment( $phpDocTop, "version", "\n# " ) . " Since: " 
        . getPhpDocComment( $phpDocTop, "since", "\n# " ) );
    diag( "Author:      " .  getPhpDocComment( $phpDocTop, "author", "\n# " ) );
    
    diag();
    // <<< Subject header -------------------------------------------------
    return( [$phpDoc, $phpDoc1]);
}   //getTestHeader()

//---------------------------------------------------------------------

/**
 * @subpackage  dumpTestData
 *
 * Dump data to files in systems temp for further analysis
 *
 * Dump are stored in files named after the caller + test numer
 * If Diff::compare() is available a diff string is returned
 *
 * This is NOT a part of Test:More, but an additional feature
 *
 * @example         dumpTestData( $This, $that );
 * 
 * @param result    The actual result from test
 * @param expected  The reference data
 * @return          A diff string else <code>FALSE</code>
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @since           2019-06-30T09:35:02
 */
function dumpTestData( $result, $expected = FALSE ) {
    global $_test_num;

    $caller = debug_backtrace();
    
    $mycaller   = $caller['0'];
    if (! strstr($caller['0']['file'], $_SERVER['PHP_SELF'])) {
        $mycaller   = $caller['1'];
    }

    $file   = str_replace($_SERVER['DOCUMENT_ROOT'], 't', $mycaller['file']);
    $line   = $mycaller['line'];

    $modulename  =  basename( $file ?? __FILE__ );
    diag( "Dumping [$modulename] no [$_test_num] line[$line]:\n" );

    $dumpfile   = sys_get_temp_dir(). "/${modulename}.${_test_num}";
    file_put_contents( "${dumpfile}.result.txt", var_export( $result, TRUE) );
    diag( "- Result:\t${dumpfile}.1.txt");
    
    if ( isset( $expected ) && ( FALSE != $expected ) ) {
        file_put_contents( "${dumpfile}.expected.txt", var_export( $expected, TRUE) );
        diag("- Reference:\t${dumpfile}.2.txt");

        echo dumpDiffData( $expected, $result );
    }
    return( FALSE );
}   // dumpTestData()


//---------------------------------------------------------------------

/**
 * @subpackage  dumpDiffData
 *
 * Dump data to files i system temp for further analysis
 *
 * Dump are stored in files named after the caller + test numer
 * If Diff::compare() is available a diff string is returned
 *
 * This is NOT a part of Test:More, but an additional feature
 *
 * @example         dumpDiffData( $This, $that );
 * 
 * @param result    The actual result from test
 * @param expected  The reference data
 * @return          A diff string else <code>FALSE</code>
 * 
 * @tutorial        https://metacpan.org/pod/Test::More
 * @since           2019-06-30T09:35:02
 */
function dumpDiffData( $result, $expected = FALSE ) {
    global $_test_num;
    $modulename = basename( $file ?? __FILE__ );
    $dumpfile   = sys_get_temp_dir(). "/${modulename}.${_test_num}";
    
    if ( method_exists( "Diff", "compare" ) ) {
            return( Diff::toString( Diff::compare( $expected, $result, FALSE) ) );
    }
    return( FALSE );
}   // dumpDiffData()


//---------------------------------------------------------------------

/**
 * @subpackage  start_buffer()
 *
 * Trap output buffers and redirect error_log to file
 *
 * @example             $error_log  = start_buffer();        //Start output buffer
 
 * @param               void
 * @return              Old buffer value

 * @tutorial            doc/manual.md
 * @see             
 * @deprecated          no
 * @since               2019-07-03T07:56:12
 */
function start_buffer() {
    $scriptlog  = '/tmp/script.log';
    global $bufferflag;     // Declare globally
    
    if ( ! isset($bufferflag) ) {   // On init
        $bufferflag = TRUE;
        if ( file_exists( $scriptlog ) )
            unlink( $scriptlog );   // Delete script log
            note( "Deleting existing [$scriptlog]");
        note( "error_log: $scriptlog" );
    }
    
    $error_log  = ini_get( "error_log" );
    ini_set("error_log", $scriptlog);
    ob_start();                     //Start output buffer
    
    return($error_log);
}   // start_buffer()

//---------------------------------------------------------------------

/**
 * @subpackage          end_buffer()
 *
 * Extract index entries
 *
 * @example             $output     = end_buffer( $error_log ); //End buffer and grab output
 
 * @param               Old buffer
 * @return              Trapped buffer as string

 * @tutorial            doc/manual.md
 * @see             
 * @deprecated          no
 * @since               2019-07-03T07:56:12
 */
function end_buffer( $error_log ) {
    $output = ob_get_contents();    //Grab output
    ob_end_clean();                 //Discard output buffer
    ini_set( "error_log", $error_log );
    return( $output );
}   // end_buffer()


//---------------------------------------------------------------------

/**
 * @subpackage      oki()
 *
 * Similar to ok(), is() and isnt() compare their two arguments 
 * with a specified operator () and expected result
 *
 * This is NOT a part of Test:More, but an additional feature
 *
 * <code>
$a= 1; $b=2;
$op= "<";  $x=TRUE; oki($a, $op, $b, $x);
$op= "=";  $x=FALSE; oki($a, $op, $b, $x);
$op= ">";  $x=FALSE; oki($a, $op, $b, $x);

$a= 2; $b=2;
$op= "<";  $x=FALSE; oki($a, $op, $b, $x);
$op= "=";  $x=TRUE; oki($a, $op, $b, $x);
$op= ">";  $x=FALSE; oki($a, $op, $b, $x);

$a= 2; $b=1;
$op= "<";  $x=FALSE; oki($a, $op, $b, $x);
$op= "=";  $x=FALSE; oki($a, $op, $b, $x);
$op= ">";  $x=TRUE; oki($a, $op, $b, $x);

 *</code>
 * 
 * @example     oki( 1, '<', 2, TRUE);
 * @example     oki( 1, '>', 2, FALSE);

 * @param a         First argument
 * @param op        Operator
 * @param b         Second argument
 * @param x         Expectet result (TRUE/false)
 * @param f         Function to do the comparison
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.

 * @tutorial        https://metacpan.org/pod/Test::More
 * @see             
 * @since           2019-07-04T08:01:06
 */
function oki($a, $op, $b, $x = TRUE, $f = "t_op") {
    $y = $f( $a, $op, $b);
    $z = ($x?' ':'!');
    ok( $z.$y, ": $a $z$op $b = [".($y?"TRUE":"false")."]");
    
    return($y);
}   // oki()


//---------------------------------------------------------------------

/**
 * @subpackage      t_op()
 *
 * Comparing two values with a specified operator
 *
 * This is NOT a part of Test:More, but an additional feature
 *
 * @example         $y = t_op( $a, $op, $b);
 * 
 * @param a         First argument
 * @param op        Operator
 * @param b         Second argument
 * @return          <code>TRUE</code> on success or <code>FALSE</code> otherwise.
 * @url             https://stackoverflow.com/a/5780522
 * @tutorial        https://metacpan.org/pod/Test::More
 * @since           2019-07-04T08:01:11
 */
function t_op($a, $op, $b) {
    switch( $op ) {
        case '<': return ($a < $b);
        case '==':
        case '=': return ($a == $b);
        case '>': return ($a > $b);
        case '+': return ($a + $b);
        default: trigger_error( "Don't understand [$a $op $b]", E_USER_WARNING);
        return( FALSE );
    }
}   // t_op()


//---------------------------------------------------------------------

/**
 * @subpackage      test_start(), test_end(), test_duration()
 *
 * Microtiming processes during test
 *
 * This is NOT a part of Test:More, but an additional feature
 *
 * @example   $timer  = array();  // Assosiative array to hold timing
 * @example   $testid   = 'testid'; // test ID
 * @example   test_start( $timer, $testid );    // Before test
 * @example   // Do something
 * @example   test_end( $timer, $testid );      // 

 * @example   $duration = test_duration( $timer, $testid );   // Display duration and return diff
 * @example   $duration = test_duration( $timer, $testid, 0.0001 );   // Display duration with short tolerance
 * @example   $duration = test_duration( $timer, $testid, 0.0001, FALSE );   // Do not display duration

 * @example   echo "Duration [$duration]" . ( $duration > 0.1 ) ? " Slower than expected" : "OK";
 * 
 * @param timer     Array with id, start and end time
 * @param testid    Tag for identifying test
 * @param estimate  Estimated max runtime
 * @param note      <code>TRUE</code> (default) will display result, <code>FALSE</code> silent otherwise.
 * @return (test_duration)  Duration in microtime
 
 * @url             https://stackoverflow.com/a/5780522
 * @tutorial        https://metacpan.org/pod/Test::More
 * @since           2019-07-04T08:01:11
 */

function test_start( &$timer, &$testid ) {
    $timer[$testid]['start']    =  microtime( TRUE );
}   // test_start()

function test_end( &$timer, &$testid ) {
    $timer[$testid]['end']    =  microtime( TRUE );
}   // test_end()

function test_duration( &$timer, &$testid, $estimate = 0.1, $note = TRUE ) {
    if ( ! isset( $timer[$testid]['end'] ))
        $timer[$testid]['end']    =  microtime( TRUE );
    
    $timer[$testid]['duration']  = $timer[$testid]['end'] - $timer[$testid]['start'];
    $timer[$testid]['estimate']  = $estimate;
    
    if ( $note )
        note( "Duration [{$timer[$testid]['duration']}] "
        .   ( ( $timer[$testid]['duration'] > $estimate ) 
            ?   "Slower than expected [$estimate]" 
            :   "OK" )
        );
    
    return( $timer[$testid]['duration'] );
}   // test_duration()

function test_result( &$timer, $note = false, $w1 = 60, $w2 = 8 ) {
    $d1=$w1;
    $d2=$w2;

    foreach ( $timer as $testname => $testprofile ) {
        if ( $testprofile['duration'] > $testprofile['estimate'] ) {
            note( 
                sprintf( "%-${w1}.${d1}s: Too slow %-0${w2}.${d2}s > %-${w2}.${d2}s", $testname, $testprofile['duration'], $testprofile['estimate'] )
                    //( 
                        //( $testprofile['duration'] <= $testprofile['estimate'] ) 
                        //?   "OK runtime (< {$testprofile['estimate']})" 
                        //:   "Too slow {$testprofile['duration']} > {$testprofile['estimate']}" 
                    //    sprintf( "Too slow %-0${w2}.${d2}s > %-${w2}.${d2}s", $testprofile['duration'], $testprofile['estimate'] ) 
                    //)
                //)
            );
        } elseif ($note) {
            note( 
                sprintf( "%-${w1}.${d1}s: OK runtime (< %s s)", $testname, $testprofile['estimate'] ) 
            );
        }
    }

}
//---------------------------------------------------------------------

?>
