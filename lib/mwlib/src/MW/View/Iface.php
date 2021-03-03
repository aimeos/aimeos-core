<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View;


/**
 * Common interface for all view classes.
 *
 * @method boolean access(string|array $groups) True if the current logged in user is in one of the given groups
 * @method \Aimeos\MW\View\Helper\Block\Iface block() Returns the block helper object
 * @method mixed config(string $name = null, string|array $default = null) Returns the config value for the given key
 * @method string content(string $path) Returns the URL for the given path (relative, absolute or data URL)
 * @method \Aimeos\MW\View\Helper\Csrf\Iface csrf() Returns the CSRF helper object
 * @method string date(string $date) Returns the formatted date
 * @method \Aimeos\MW\View\Helper\Encoder\Iface encoder() Returns the encoder helper object
 * @method string formparam(string|array $names) Returns the name for the HTML form parameter
 * @method \Aimeos\MW\Mail\Message\Iface mail() Returns the e-mail message object
 * @method string number(integer|float|decimal $number, integer $decimals = null) Returns the formatted number
 * @method string|array param(string|null $name, string|array $default) Parameter value or associative list of key/value pairs
 * @method string partial(string $filepath, array $params = []) Renders the rendered partial template
 * @method \Aimeos\MW\View\Helper\Request\Iface request() Returns the request view helper object
 * @method \Aimeos\MW\View\Helper\Response\Iface response() Returns the response view helper object
 * @method mixed session($name, $default = null) Returns the session value for the given name
 * @method string translate(string $domain, string $singular, string $plural = '', integer $number = 1) Returns the translated string or the original one if no translation is available
 * @method string url(string|null $target, string|null $controller = null, string|null $action = null, array $params = [], array $trailing = [], array $config = []) Returns the URL assembled from the given arguments
 * @method mixed value(array $values, $key, $default = null) Returns the value for the given key in the array
 *
 * @package MW
 * @subpackage View
 */
interface Iface
{
	/**
	 * Calls the view helper with the given name and arguments and returns it's output.
	 *
	 * @param string $name Name of the view helper
	 * @param array $args Arguments passed to the view helper
	 * @return mixed Output depending on the view helper
	 */
	public function __call( string $name, array $args );

	/**
	 * Returns the value associated to the given key.
	 *
	 * @param string $key Name of the value that should be returned
	 * @return mixed Value associated to the given key
	 * @throws \Aimeos\MW\View\Exception If the requested key isn't available
	 */
	public function __get( string $key );

	/**
	 * Tests if a key with the given name exists.
	 *
	 * @param string $key Name of the value that should be tested
	 * @return bool True if the key exists, false if not
	 */
	public function __isset( string $key ) : bool;

	/**
	 * Removes a key from the stored values.
	 *
	 * @param string $key Name of the value that should be removed
	 * @return void
	 */
	public function __unset( string $key );

	/**
	 * Sets a new value for the given key.
	 *
	 * @param string $key Name of the value that should be set
	 * @param mixed $value Value associated to the given key
	 * @return void
	 */
	public function __set( string $key, $value );

	/**
	 * Adds a view helper instance to the view.
	 *
	 * @param string $name Name of the view helper as called in the template
	 * @param \Aimeos\MW\View\Helper\Iface $helper View helper instance
	 * @return \Aimeos\MW\View\Iface View object for method chaining
	 */
	public function addHelper( string $name, \Aimeos\MW\View\Helper\Iface $helper ) : Iface;

	/**
	 * Assigns a whole set of values at once to the view.
	 * This method overwrites already existing key/value pairs set by the magic method.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @return \Aimeos\MW\View\Iface View object for method chaining
	 */
	public function assign( array $values ) : Iface;

	/**
	 * Returns the value associated to the given key or the default value if the key is not available.
	 *
	 * @param string $key Name of the value that should be returned
	 * @param mixed $default Default value returned if this key is not available
	 * @return mixed Value associated to the given key or the default value
	 */
	public function get( string $key, $default = null );

	/**
	 * Assigns the value to the given key in the view.
	 *
	 * @param string $key Name of the key that should be set
	 * @param mixed $value Value that should be assigned to the key
	 * @return \Aimeos\MW\View\Iface View object for method chaining
	 */
	public function set( string $key, $value );

	/**
	 * Renders the output based on the given template file name and the key/value pairs.
	 *
	 * @param array|string $filenames File name or list of file names for the view templates
	 * @return string Output generated by the template or null for none
	 * @throws \Aimeos\MW\View\Exception If the template isn't found
	 */
	public function render( $filename ) : string;
}
