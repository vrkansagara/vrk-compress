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


// make sure the plugin does not expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
    if ( ! headers_sent() ) {
        if ( function_exists( 'http_response_code' ) ) {
            http_response_code( 403 );
        } else {
            header( 'HTTP/1.1 403 Forbidden', true, 403 );
        }
    }
    exit( 'Hi there! I am a WordPress plugin requiring functions included with WordPress. I am not meant to be addressed directly.' );
}

// plugin requires PHP 5.6 or greater
if ( version_compare( PHP_VERSION, '5.6.0', '<' ) ) {
    if ( ! class_exists( 'Vrk_CompatibilityNotice' ) ) {
        require_once( dirname( __FILE__ ) . '/compatibility-notice.php' );
    }

    // possibly display a notice, trigger error
    add_action( 'admin_init', array( 'Vrk_CompatibilityNotice', 'adminInit' ) );

    // stop execution of this file
    return;
}

// PHP namespace autoloader
require_once( dirname( __FILE__ ) . '/autoload.php' );

// initialize on plugins loaded
add_action(
    'plugins_loaded',
    array( '\Vrk\Compressor\PluginLoader', 'init' ),
    0, // priority
    0 // expected arguments
);




function getCompressedOutPut($buffer)
{
    if (strpos($buffer, '<pre>') !== false) {
        $replace = array(
            '/<!--[^\[](.*?)[^\]]-->/s' => '',
            "/<\?php/" => '<?php ',
            "/\r/" => '',
            "/>\n</" => '><',
            "/>\s+\n</" => '><',
            "/>\n\s+</" => '><'
        );
    } else {
        $replace = array(
            '/<!--[^\[](.*?)[^\]]-->/s' => '',
            "/<\?php/" => '<?php ',
            "/\n([\S])/" => '$1',
            "/\r/" => '',
            "/\n/" => '',
            "/\t/" => '',
            "/ +/" => ' '
        );
    }

    // Remove html comment;
    $additionaly = array(
        // strip whitespaces after tags, except space
        '/\>[^\S ]+/s' => '>',
        // strip whitespaces before tags, except space
        '/[^\S ]+\</s' => '<',
        // shorten multiple whitespace sequences
        '/(\s)+/s' => '\\1',
        // Remove html comment
        '!/\*.*?\*/!s' => '',
        '/\n\s*\n/' => ''
    );
     //$buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);
     $buffer = preg_replace('/<!--(.*)-->/Uis', '', $buffer);
    $buffer = preg_replace(array_keys($additionaly), array_values($additionaly), $buffer);
    ini_set("pcre.recursion_limit", "16777");
//    ini_set('zlib.output_compression', 'On'); // If you like to enable GZip, too!
    $buffer = compress($buffer);
    return $buffer;
}
function compress($buffer)
{
    /**
     * To remove useless whitespace from generated HTML, except for Javascript.
     * [Regex Source]
     * https://github.com/bcit-ci/codeigniter/wiki/compress-html-output
     * http://stackoverflow.com/questions/5312349/minifying-final-html-output-using-regular-expressions-with-codeigniter
     */
    $regexRemoveWhiteSpace = '%# Collapse ws everywhere but in blacklisted elements.
        (?>             # Match all whitespaces other than single space.
          [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
        | \s{2,}        # or two or more consecutive-any-whitespace.
        ) # Note: The remaining regex consumes no text at all...
        (?=             # Ensure we are not in a blacklist tag.
          (?:           # Begin (unnecessary) group.
            (?:         # Zero or more of...
              [^<]++    # Either one or more non-"<"
            | <         # or a < starting a non-blacklist tag.
              (?!/?(?:textarea|pre)\b)
            )*+         # (This could be "unroll-the-loop"ified.)
          )             # End (unnecessary) group.
          (?:           # Begin alternation group.
            <           # Either a blacklist start tag.
            (?>textarea|pre)\b
          | \z          # or end of file.
          )             # End alternation group.
        )  # If we made it here, we are not in a blacklist tag.
        %ix';
    $regexRemoveWhiteSpace = '%(?>[^\S ]\s*| \s{2,})(?=(?:(?:[^<]++| <(?!/?(?:textarea|pre)\b))*+)(?:<(?>textarea|pre)\b|\z))%ix';
    $re = '%# Collapse whitespace everywhere but in blacklisted elements.
        (?>             # Match all whitespans other than single space.
          [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
        | \s{2,}        # or two or more consecutive-any-whitespace.
        ) # Note: The remaining regex consumes no text at all...
        (?=             # Ensure we are not in a blacklist tag.
          [^<]*+        # Either zero or more non-"<" {normal*}
          (?:           # Begin {(special normal*)*} construct
            <           # or a < starting a non-blacklist tag.
            (?!/?(?:textarea|pre|script)\b)
            [^<]*+      # more non-"<" {normal*}
          )*+           # Finish "unrolling-the-loop"
          (?:           # Begin alternation group.
            <           # Either a blacklist start tag.
            (?>textarea|pre|script)\b
          | \z          # or end of file.
          )             # End alternation group.
        )  # If we made it here, we are not in a blacklist tag.
        %Six';

    // $new_buffer = preg_replace('/<!--(.*|\n)-->/Uis', " ", sanitize_output($buffer));
    // $new_buffer = preg_replace('/\s+/', " ", sanitize_output($new_buffer));
    $new_buffer = preg_replace($regexRemoveWhiteSpace, " ", sanitize_output($buffer));

    // We are going to check if processing has working
    if ($new_buffer === null) {
        $new_buffer = $buffer;
    }

    return $new_buffer;
}
function sanitize_output($buffer)
{
    $search = array(
        '/\>[^\S ]+/s', // strip whitespaces after tags, except space
        '/[^\S ]+\</s', // strip whitespaces before tags, except space
        '/(\s)+/s', // shorten multiple whitespace sequences
        '!/\*.*?\*/!s', // Remove htmlcomment
        '/\n\s*\n/'
    ); // Remove htmlcomment

    $replace = array(
        '>',
        '<',
        '\\1',
        '',
        ''
    );
    $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}
