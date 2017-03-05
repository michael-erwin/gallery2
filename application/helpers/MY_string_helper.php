<?php

function js_string($subject)
{
    $pattern = ['/[\t\r\n]/','/\s+/','/"/','/<\//'];
    $replace = ['',' ','\"','<\/'];
    return preg_replace($pattern, $replace, $subject);
}

// Removes new line and tabs.
function clean_whitespace($subject)
{
    $pattern = ["/[\n\r\t]/","/\s\s+/"];
    $replace = ['',' '];
    return preg_replace($pattern,$replace,$subject);
}

// Integer only. i.e. 56
function clean_numeric_text($subject)
{
    $pattern = '/[^0-9]/';
    $replace = '';
    return preg_replace($pattern, $replace, $subject);
}

// Integer and float only. i.e. 23.54
function clean_float_text($subject)
{
    $pattern = '/[^0-9\.]/';
    $replace = '';
    return preg_replace($pattern, $replace, $subject);
}

// Letters only. i.e. username
function clean_alpha_text($subject)
{
    $pattern = '/[^a-zA-Z]/';
    $replace = '';
    return preg_replace($pattern, $replace, $subject);
}

// Letters and numbers only.
function clean_alphanum_hash($subject)
{
    $pattern = '/[^a-zA-Z0-9]/';
    $replace = '';
    return preg_replace($pattern, $replace, $subject);
}

// Letters, integers, dash, space, underscore. i.e. All 0-3 quick_brown fox.
function clean_title_text($subject)
{
    $pattern = '/[^a-zA-Z0-9 \-_]/';
    $replace = '';
    return preg_replace($pattern, $replace, $subject);
}

function clean_body_text($subject)
{
    $pattern = '/[^a-zA-Z0-9 \.\-_]/';
    $replace = '';
    return preg_replace($pattern, $replace, $subject);
}

// Removes new line, double space, tabs.
function compress_html($subject)
{
    $pattern = ["/[\n\r\t]/","/\s{2,}/"];
    $replace = ['',''];
    return preg_replace($pattern,$replace,$subject);
}
