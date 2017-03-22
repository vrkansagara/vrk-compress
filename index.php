<?php
/*
 * Date: 22/3/17
 * Time: 10:40 AM
 * Plugin Name: wp-compressor
 * Plugin URI: https://vrkansagara.in/plugin/wp-compressor
 * Description: Compress all final output
 * Version: 1.0
 * Author: Vallabh Kansagara
 * Author URI: https://vrkansagara.in/author/vrk
 * Author Email: vrkansagara@gmail.com
*/

include_once  __DIR__.'/compress.php';

// This code alwayse be the last in function.php file.
/**
 * Modify final html output before sending to the browser for rendering process.
 * @param $buffer
 * @return mixed
 */
function callback($buffer) {
    $buffer = getCompressedOutPut($buffer);
//    $buffer = compress($buffer);
    // modify buffer here, and then return the updated code
    return $buffer;
}

add_action('template_redirect', 'foo_buffer_go', 0);
function foo_buffer_go(){
    ob_start('callback');
}

add_action('shutdown', 'foo_buffer_stop', 1000);
function foo_buffer_stop(){
    ob_end_flush();
}
