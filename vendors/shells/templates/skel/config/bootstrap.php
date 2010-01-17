<?php
/**
 * Ensure the TMP structure exists. Particularly useful when using dynamic subdomains, and defining
 * in webroot/index.php TMP to include the name of the domain and therefore eliminate the risk
 * of cache confusion
 */
if (!is_dir(TMP . 'cache/persistent')) {
	exec('mkdir -p ' . TMP . 'cache/data');
	exec('mkdir -p ' . TMP . 'cache/models');
	exec('mkdir -p ' . TMP . 'cache/persistent');
	exec('mkdir -p ' . TMP . 'cache/url');
	exec('mkdir -p ' . TMP . 'cache/views');
	exec('mkdir -p ' . TMP . 'logs');
	exec('mkdir -p ' . TMP . 'sessions');
	exec('mkdir -p ' . TMP . 'tests');
}

define('DEFAULT_LANGUAGE', 'eng');
Configure::write('Redirect.model', 'Redirect');

/**
 * If it's not development mode, route asset/media requests to a static subdomain.
 * Skip the favicon and debug kit (which sets cookies) an server everything else from one of 3
 * static subdomains - using the first char of the md5 for the request to determine which subdomain
 * to use.
 *
 * Other examples:
 * Server everything from a single static subdomain:

	if (isProduction()) {
		Configure::write('Asset.hosts', array(
			0 => array(
				'@^(.*)@' =>'http://static.example.com'
			),
		));
	}

 * ignore the favicon and debug kit, delegate to another function entirely:

	if (isProduction()) {
		Configure::write('Asset.hosts', array(
			'whatDomain' => array(
				'@^(.*)@' =>'{1}'
			),
		));
	}
	function whatDomain(&$file) {
		if ( .... ) {
			$file = 'differentname.jpg';
			return 'http://thisone';
		} elseif ( ... ) {
			return 'http://thatone';
		}
		return;
	}
 *
 */
	if (!isDevelopment()) {
		Configure::write('Asset.hosts', array(
			0 => array(
				'@^/?(favicon.ico|debug_kit.*)@' => '',
			),
			'md5' => array(
				'@^[0-5]@' =>'http://static1.skel',
				'@^[6-a]@' =>'http://static2.skel',
				'@^.@' =>'http://static3.skel',
			)
		));
	}

/**
 * isproduction method
 * a stub/example
 *
 * @return boolean
 * @access public
 */
function isproduction() {
	return APP_DIR === 'live';
}

/**
 * isstaging method
 * a stub/example
 *
 * @return boolean
 * @access public
 */
function isstaging() {
	return APP_DIR === 'staging';
}

/**
 * isdevelopment method
 * a stub/example
 *
 * @return boolean
 * @access public
 */
function isdevelopment() {
	return (!isproduction() && !isstaging());
}