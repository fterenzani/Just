<?php

/**
 * Let's set where we are
 */
$inc_path = realpath(dirname(__FILE__) . '/../includes');
$app_path = dirname(__FILE__);

set_include_path($app_path . PATH_SEPARATOR . $inc_path . PATH_SEPARATOR . get_include_path());


/**
 * What time is it Bob?
 */
date_default_timezone_set('Europe/Rome');



/**
 * The request to route
 */
$request = explode('?', $_SERVER['REQUEST_URI']);
$request = preg_replace('#' . $config->web . '(?:' . $config->front_file . '/)?(.*)#', "/$1", $request[0]);


/**
 * Check if the request match
 */
foreach($router as $route) {

	if ($params = $route->match($request)) {
		break;

	}

}

if ($params === false || !file_exists(dirname(__FILE__) . '/modules/' . $params['module'] . '/_' . $params['action'] . '.php')) {
	require 'errors/404.php';

} else {
	try {
		require 'modules/' . $params['module'] . '/_' . $params['action'] . '.php';

		if ($config->layout) {
			$content = ob_get_clean();
			require $config->layout;

		} else {
			@ob_end_flush();

        }

	} catch(Exception $exception) {
		require 'errors/500.php';

	}
}