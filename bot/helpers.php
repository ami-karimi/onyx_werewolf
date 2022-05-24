<?php

// Error handling

/**
 * Error handler, passes flow over the exception logger with new ErrorException.
 *
 * @param $num
 * @param $str
 * @param $file
 * @param $line
 * @param null $context
 */
function log_error( $num, $str, $file, $line, $context = null )  {
    log_exception( new ErrorException( $str, 0, $num, $file, $line ) );
}

/**
 * Uncaught exception handler.
 * @param \Exception $e
 */
function log_exception( $e )  {

    // setup notifier
    $API_KEY  = 'BOT_TOKEN_LOG'; // Replace 'XXXXXXXXXX' with your bot's API token
    $DEV_ID   = 'LOG_GROUP_ID'; // Replace 'XXXXXXXXXX' with your Telegram user ID (use /whoami command)

    // get incomming message
    $incoming = file_get_contents('php://input');

    // if message exist convert it into array
    $incoming = !empty($incoming) ? json_decode(file_get_contents('php://input'), true) : false ;

    // developer notification message text
    $message  = get_class( $e ) . " - <b>{$e->getMessage()}</b>;".PHP_EOL."File: <b>{$e->getFile()}</b>; Line: <b>{$e->getLine()}</b>; Time: <b>".date("H:i:s / d.m.Y")."</b>;".PHP_EOL."<b>Incoming message:</b><pre>".(!empty($incoming)?var_export($incoming, true).'</pre>':'</pre>'.PHP_EOL.'<b>Trace:</b><pre>'.$e->getTraceAsString().'</pre>');

    // developer notification message settings
    $fields_string = '';
    $url = 'https://api.telegram.org/bot'.$API_KEY.'/sendMessage';

    $fields = [
        'chat_id' => urlencode($DEV_ID),
        'parse_mode' => urlencode('HTML'),
        'text' => urlencode(''.$message)
    ];

    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

    //execute post
    $result = curl_exec($ch);

    //close connection
    curl_close($ch);

    // Uncomment following line and change path to store errors log in custom file
    // file_put_contents( __DIR__ .'/custom_errors.log', ($result?'Notified: '.var_export($result, true).PHP_EOL:'Not notified: '.var_export($result, true).PHP_EOL).$message . PHP_EOL, FILE_APPEND );

    // Sending 200 response code
    header('X-PHP-Response-Code: 200', true, 200);

    exit();
}

/**
 * Checks for a fatal error, work around for set_error_handler not working on fatal errors.
 */
function check_for_fatal()
{
    $error = error_get_last();
    if($error)
    if ( $error["type"] === E_ERROR   )
        log_error( $error["type"], $error["message"], $error["file"], $error["line"] );
}

register_shutdown_function( "check_for_fatal" );
set_error_handler( "log_error" );
set_exception_handler( "log_exception" );
