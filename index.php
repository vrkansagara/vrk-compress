<?php
/*
 * Plugin Name: VRK Compressor
 * Plugin URI: https://vrkansagara.in/plugin/vrk-compressor
 * Description: Compress all final output
 * Version: 1.0
 * Author: Vallabh Kansagara
 * Author URI: https://vrkansagara.in/author/vrk
 * Author Email: vrkansagara@gmail.com
*/

/*
BSD 3-Clause License

Copyright (c) 2017, Vallabh Kansagara
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this
  list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

* Neither the name of the copyright holder nor the names of its
  contributors may be used to endorse or promote products derived from
  this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

// Silence is golden.


include_once  __DIR__.'/compress.php';

function callback($buffer) {
    if ( !is_user_logged_in()) {
        $buffer = getCompressedOutPut($buffer);
    }
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

// Disable W3TC footer comment for all users
add_filter( 'w3tc_can_print_comment', '__return_false', 10, 1 );


function remove_head_scripts() { 
remove_action('wp_head', 'wp_print_scripts'); 
remove_action('wp_head', 'wp_print_head_scripts', 9); 
remove_action('wp_head', 'wp_enqueue_scripts', 1);

add_action('wp_footer', 'wp_print_scripts', 5);
add_action('wp_footer', 'wp_enqueue_scripts', 5);
add_action('wp_footer', 'wp_print_head_scripts', 5); 
} 
add_action( 'wp_enqueue_scripts', 'remove_head_scripts' );



add_filter('gettext', 'change_howdy', 10, 3);

function change_howdy($translated, $text, $domain) {

    if (!is_admin() || 'default' != $domain)
        return $translated;

    if (false !== strpos($translated, 'Howdy'))
        return str_replace('Howdy', 'Welcome', $translated);

    return $translated;
}


add_action( 'wp_dashboard_setup', 'register_my_dashboard_widget' );
function register_my_dashboard_widget() {
    wp_add_dashboard_widget(
        'vrk_dashboard_widget',
        'VRK Compressor',
        'vrk_dashboard_widget_display'
    );

}

function vrk_dashboard_widget_display() {
    echo 'Hello, VRK Compressor';
}
