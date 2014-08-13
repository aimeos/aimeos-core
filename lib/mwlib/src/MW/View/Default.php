<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 */


/**
 * Default view implementation.
 *
 * @method string|array config(string $name = null, string|array $default = null) Returns the config value for the given key
 * @method string date(string $date) Returns the formatted date
 * @method MW_View_Helper_Interface encoder() Returns the encoder object
 * @method string formparam(string|array $names) Returns the name for the HTML form parameter
 * @method MW_Mail_Message_Interface mail() Returns the e-mail message object
 * @method string number(integer|float|decimal $number, integer $decimals) Returns the formatted number
 * @method string|array param(string|null $name, string|array $default) Returns the parameter value
 * @method string translate(string $domain, string $singular, string $plural = '', integer $number = 1) Returns the translated string or the original one if no translation is available
 * @method string url(string|null $target, string|null $controller = null, string|null $action = null, array $params = array(), array $trailing = array(), array $config = array()) Returns the URL assembled from the given arguments
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Default implements MW_View_Interface
{
	private $_helper = array();
	private $_values = array();


	/**
	 * Calls the view helper with the given name and arguments and returns it's output.
	 *
	 * @param string $name Name of the view helper
	 * @param array $args Arguments passed to the view helper
	 * @return mixed Output depending on the view helper
	 */
	public function __call( $name, array $args )
	{
		if( !isset( $this->_helper[$name] ) )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? 'MW_View_Helper_' . $name : '<not a string>';
				throw new MW_View_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
			}

			$iface = 'MW_View_Helper_Interface';
			$classname = 'MW_View_Helper_' . ucfirst( $name ) . '_Default';

			if( class_exists( $classname ) === false ) {
				throw new MW_View_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$helper = new $classname( $this );

			if( !( $helper instanceof $iface ) ) {
				throw new MW_View_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
			}

			$this->_helper[$name] = $helper;
		}

		return call_user_func_array( array( $this->_helper[$name], 'transform' ), $args );
	}


	/**
	 * Clones internal objects of the view.
	 */
	public function __clone()
	{
		foreach( $this->_helper as $name => $helper ) {
			$this->_helper[$name] = clone $helper;
		}
	}


	/**
	 * Returns the value associated to the given key.
	 *
	 * @param string $key Name of the value that should be returned
	 * @return mixed Value associated to the given key
	 * @throws MW_View_Exception If the requested key isn't available
	 */
	public function __get( $key )
	{
		if( !isset( $this->_values[$key] ) ) {
			throw new MW_View_Exception( sprintf( 'No value for key "%1$s" found', $key ) );
		}

		return $this->_values[$key];
	}


	/**
	 * Tests if a key with the given name exists.
	 *
	 * @param string $key Name of the value that should be tested
	 * @return boolean True if the key exists, false if not
	 */
	public function __isset( $key )
	{
		return isset( $this->_values[$key] );
	}


	/**
	 * Removes a key from the stored values.
	 *
	 * @param string $key Name of the value that should be removed
	 */
	public function __unset( $key )
	{
		unset( $this->_values[$key] );
	}


	/**
	 * Sets a new value for the given key.
	 *
	 * @param string $key Name of the value that should be set
	 * @param mixed $value Value associated to the given key
	 */
	public function __set( $key, $value )
	{
		$this->_values[$key] = $value;
	}


	/**
	 * Adds a view helper instance to the view.
	 *
	 * @param string $name Name of the view helper as called in the template
	 * @param MW_View_Helper_Interface $helper View helper instance
	 */
	public function addHelper( $name, MW_View_Helper_Interface $helper )
	{
		$this->_helper[$name] = $helper;
	}


	/**
	 * Assigns a whole set of values at once to the view.
	 * This method overwrites already existing key/value pairs set by the magic method.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function assign( array $values )
	{
		$this->_values = $values;
	}


	/**
	 * Returns the value associated to the given key or the default value if the key is not available.
	 *
	 * @param string $key Name of the value that should be returned
	 * @param mixed $default Default value returned if ths key is not available
	 * @return mixed Value associated to the given key or the default value
	 */
	public function get( $key, $default = null )
	{
		if( isset( $this->_values[$key] ) ) {
			return $this->_values[$key];
		}

		return $default;
	}


	/**
	 * Renders the output based on the given template file name and the key/value pairs.
	 *
	 * @param string $filename File name of the view template
	 * @return string Output generated by the template
	 * @throws MW_View_Exception If the template isn't found
	 */
	public function render( $filename )
	{
		try
		{
			ob_start();

			$this->_include( $filename );

			return ob_get_clean();
		}
		catch( Exception $e )
		{
			ob_end_clean();
			throw $e;
		}
	}


	/**
	 * Includes the template file and processes the PHP instructions.
	 * The filename is passed as first argument but without variable name to prevent messing the variable scope.
	 */
	protected function _include()
	{
		include func_get_arg( 0 );
	}
}
