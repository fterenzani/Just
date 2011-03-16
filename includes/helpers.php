<?php

function redirect_for($name, $args = array()) {
    redirect(url_for($name, $args));
}

/**
 * Return an internal app link
 *
 * @param string $name the name of a route defined in file $app . '/routing.php'
 * @param mixing $args variables to construct the internal url
 * @param boolean $absolute if or not to include the domain name
 * @return string an internal url
 */

function path_for($name, $args = array()) {
    global $router, $config;

    // discover the path
    if (isset($router->{$name})) {

        $path = $router->{$name}->path($args);

    } elseif ($name === 'homepage') {

        $path = '/';

    } else {

        $path = $name;

    }

    // create the link
    if (!$config->front_file) {
        $path = ltrim($path, '/');
    }

    if ($config->front_file === 'index.php') {

        if ($path === '/') {

            return $config->web;

        } else {
            return $config->web . $config->front_file . $path;

        }

    } else {

        return $config->web . $config->front_file . $path;

    }

}

function url_for($name, $args = array(), $schema = 'http://') {
    return $schema . $_SERVER['HTTP_HOST'] . path_for($name, $args);

}

function set_flash($type, $message) {

    $_SESSION[$type] = $message;

}

function has_flash($type) {

    return isset($_SESSION[$type]);

}

function get_flash($type) {
    $message = $_SESSION[$type];
    $_SESSION[$type] = NULL;

    return $message;

}


function set_status($code, $msg = NULL) {
    $msgs = array(
            '204' => 'No Content',

            '301' => 'Moved Permanently',
            '302' => 'Found',
            '304' => 'Not Modified',

            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '403' => 'Forbidden',
            '404' => 'Not Found',

            '500' => 'Internal Server Error',
    );

    if (!$msg) {

        if (isset($msgs[$code]))
            $msg = $msgs[$code];

    }

    header("HTTP/1.1 $code $msg");

}

function redirect($location, $status = '302') {
    if ($status != '302') {
        set_status($status);
    }
    header('Location: ' . $location);
    die;
}

function slugify($text) {

    $text = htmlspecialchars_decode($text, ENT_QUOTES);

    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

    // trim
    $text = trim($text, '-');

    // transliterate
    if (function_exists('iconv')) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }

    // lowercase
    $text = strtolower($text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}


function input($key = null, $default = null) {
    return _input('_REQUEST', $key, $default);
}

function input_get($key = null, $default = null) {
    return _input('_GET', $key, $default);
}

function input_post($key = null, $default = null) {
    return _input('_POST', $key, $default);
}

function input_cookie($key = null, $default = null) {
    return _input('_COOKIE', $key, $default);
}

function input_server($key = null, $default = null) {
    return _input('_SERVER', $key, $default);
}

function _input($type, $key, $default) {

    $input = ${$type};
    if (!$key) {
        return _input_filter($input);

    } else {
        return isset($input[$key])? _input_filter($input[$key]): $default;

    }

}

function _input_filter($data) {


    if (is_string($data)) {
        $out = htmlspecialchars($data, ENT_QUOTES);

    } elseif (is_array($data)) {
        foreach ($data as $key => $value) {
            $out[_input_filter($key)] = _input_filter($value);

        }

    } else {
        $out = $data;

    }

    return $out;

}