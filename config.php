<?php

// error_reporting(E_ALL ^ E_WARNING);

if (!defined('LIVE'))
    DEFINE('LIVE', TRUE);


// Errors are emailed here:
DEFINE('CONTACT_EMAIL', 'email@gmail.com');
define('BASE_URI', dirname($_SERVER['DOCUMENT_ROOT'])  .'/');   // /home/vagrant/Code/Project

define('BASE_URL', $_SERVER['SERVER_NAME']. '/');               // homestead.test
// define('PDO', BASE_URI . 'db-connection-ajx.php');

define('ROOT', $_SERVER['DOCUMENT_ROOT']); // /home/vagrant/Code/Project/public
   
define('VIEWS', ROOT . '/views/');
define('INCLUDES', BASE_URI . 'includes/');
define('CLASSES', BASE_URI . 'classes/');

define('LAYOUTS', ROOT . '/layouts/');




// autolaod Classes
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});


/* 
 * Function for handling errors.
 * Takes five arguments: error number, error message (string), name of the file where the error occurred (string) 
 * line number where the error occurred, and the variables that existed at the time (array).
 * Returns true.
 */
function my_error_handler($e_number, $e_message, $e_file, $e_line, $e_vars) {

    $message = "An error occurred in script '$e_file' on line $e_line:\n$e_message\n";

    // Add the backtrace:
    $message .= "<pre>" . print_r(debug_backtrace(), 1) . "</pre>\n";

    // Or just append $e_vars to the message:
    //	$message .= "<pre>" . print_r ($e_vars, 1) . "</pre>\n";

    if (!LIVE) { // Show the error in the browser.
        
        echo '<div class="alert alert-danger">' . nl2br($message) . '</div>';
    } else { // Development (print the error to log file).
        // Send the error in an email:
        error_log($message, 1, CONTACT_EMAIL, 'From:sales@dobaln.co.uk');

        //DEBUGGIN ONLY
        //echo $message;
        
        if ($e_number != E_NOTICE) { // Only print an error message in the browser, if the error isn't a notice

            echo '<div class="alert alert-danger">A system error occurred. We apologize for the inconvenience.</div>';

            //TESTING ONLY BELOW, REMOVE
             echo $message;
        }
    } 

    return true; // So that PHP doesn't try to handle the error, too.
}

// Use my error handler:
set_error_handler('my_error_handler');




/*
 * PDO Uncaught Exception Handler
 * -----------------------------
 * user-defined function to handle all uncaught exceptions:
 */
function handleMissedException($e) {
    echo "An exception error occured, we apologize";
     echo '<h3> Unhandled Exception Sam: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' on line ' . $e->getLine(). '</h3>'; //DEBUG ONLY
    //error_log('Unhandled Exception Sam: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' on line ' . $e->getLine());
}
set_exception_handler('handleMissedException');



// Omit the closing PHP tag to avoid 'headers already sent' errors!

