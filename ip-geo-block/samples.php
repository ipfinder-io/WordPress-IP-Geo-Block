<?php
/**
 * Code samples/snippets for functions.php
 * to extend functionality of IP Geo Block
 *
 * @package   IP_Geo_Block
 * @author    tokkonopapa <tokkonopapa@yahoo.com>
 * @license   GPL-2.0+
 * @link      http://tokkono.cute.coocan.jp/blog/slow/
 * @copyright 2014 tokkonopapa
 */

if ( class_exists( 'IP_Geo_Block' ) ):

/**
 * Example1: usage of 'ip-geo-block-remote-ip'
 * Use case: replace ip address for test purpose
 *
 * @param  string $ip original ip address
 * @return string $ip replaced ip address
 */
function my_replace_ip( $ip ) {
	return '98.139.183.24'; // yahoo.com
}
add_filter( 'ip-geo-block-remote-ip', 'my_replace_ip' );


/**
 * Example2: usage of 'ip-geo-block-remote-ip'
 * Use case: retrieve ip address behind the proxy
 *
 * @param  string $ip original ip address
 * @return string $ip replaced ip address
 */
function my_retrieve_ip( $ip ) {
	if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
		$ip = trim( $ip[0] );
	}

	return $ip;
}
add_filter( 'ip-geo-block-remote-ip', 'my_retrieve_ip' );


/**
 * Example3: usage of 'ip-geo-block-headers'
 * Use case: change the user agent strings when accessing remote contents
 *
 * @param  string $args http request headers for `wp_remote_get()`
 * @return string $args http request headers for `wp_remote_get()`
 */
function my_user_agent( $args ) {
    $args['user-agent'] = 'my user agent strings';
    return $args;
}
add_filter( 'ip-geo-block-headers', 'my_user_agent' );


/**
 * Example4: usage of 'ip-geo-block-maxmind-path'
 * Use case: change the path to Maxmind database files to writable directory
 *
 * @param  string $path original path to database files
 * @return string $path replaced path to database files
 */
function my_maxmind_path( $path ) {
	$upload = wp_upload_dir();
	return $upload['basedir'];
}
add_filter( 'ip-geo-block-maxmind-path', 'my_maxmind_path' );


/**
 * Example5: usage of 'ip-geo-block-maxmind-zip-ipv[46]'
 * Use case: replace Maxmind database files to city edition
 *
 * @param  string $url original url to zip file
 * @return string $url replaced url to zip file
 */
function my_maxmind_ipv4( $url ) {
	return 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz';
}
function my_maxmind_ipv6( $url ) {
	return 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCityv6-beta/GeoLiteCityv6.dat.gz';
}
add_filter( 'ip-geo-block-maxmind-zip-ipv4', 'my_maxmind_ipv4' );
add_filter( 'ip-geo-block-maxmind-zip-ipv6', 'my_maxmind_ipv6' );


/**
 * Example6: usage of 'ip-geo-block-comment'
 * Use case: exclude specific countries in the blacklist on comment post
 *
 * @param  string $validate['ip'] ip address
 * @param  string $validate['code'] country code
 * @return array $validate add 'result' as 'passed' or 'blocked' if possible
 */
function my_ip_blacklist( $validate ) {
	$blacklist = array(
		'123.456.789.',
	);

	foreach ( $blacklist as $ip ) {
		if ( strpos( $ip, $validate['ip'] ) === 0 ) {
			$validate['result'] = 'blocked';
			break;
		}
	}

	return $validate;
}
add_filter( 'ip-geo-block-comment', 'my_ip_blacklist' );


/**
 * Example7: usage of 'ip-geo-block-login'
 * Use case: allow login only from specific ip addresses in the whitelist
 * (validate ip address to exclude Brute-force attack on login process)
 *
 * @param  string $validate['ip'] ip address
 * @param  string $validate['code'] country code
 * @return array $validate add 'result' as 'passed' or 'blocked' if possible
 */
function my_whitelist( $validate ) {
	$whitelist = array(
		'JP',
	);

	$validate['result'] = 'blocked';

	foreach ( $whitelist as $country ) {
		if ( strtoupper( $country ) === $validate['code'] ) {
			$validate['result'] = 'passed';
			break;
		}
	}

	return $validate;
}
add_filter( 'ip-geo-block-login', 'my_whitelist' ); // hook custom filter


/**
 * Example8: validate ip address before authrization of admin
 * Use case: limit the ip addresses that can access the admin area except ajax
 *
 */
add_filter( 'ip-geo-block-admin', 'my_whitelist' ); // hook custom filter

endif;