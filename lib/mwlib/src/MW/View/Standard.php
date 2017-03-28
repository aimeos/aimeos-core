<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View;


/**
 * Default view implementation.
 *
 * @method mixed config(string $name = null, string|array $default = null) Returns the config value for the given key
 * @method \Aimeos\MW\View\Helper\Iface csrf() Returns the CSRF helper object
 * @method string date(string $date) Returns the formatted date
 * @method \Aimeos\MW\View\Helper\Iface encoder() Returns the encoder helper object
 * @method string formparam(string|array $names) Returns the name for the HTML form parameter
 * @method \Aimeos\MW\Mail\Message\Iface mail() Returns the e-mail message object
 * @method string number(integer|float|decimal $number, integer $decimals = 2) Returns the formatted number
 * @method string|array param(string|null $name, string|array $default) Returns the parameter value
 * @method string partial(string $filepath, array $params = [] ) Renders the partial template
 * @method \Aimeos\MW\View\Helper\Iface request() Returns the request helper object
 * @method string translate(string $domain, string $singular, string $plural = '', integer $number = 1) Returns the translated string or the original one if no translation is available
 * @method string url(string|null $target, string|null $controller = null, string|null $action = null, array $params = [], array $trailing = [], array $config = []) Returns the URL assembled from the given arguments
 *
 * @package MW
 * @subpackage View
 */
class Standard implements \Aimeos\MW\View\Iface
{
	private $helper = [];
	private $values = [];
	private $engines;
	private $paths;


	/**
	 * Initializes the view object
	 *
	 * @param array $paths Associative list of base paths as keys and list of relative paths as value
	 * @param array $engines Associative list of file extensions as keys and \Aimeos\MW\View\Engine\Iface as value
	 */
	public function __construct( array $paths = [], array $engines = [] )
	{
		$this->engines = $engines;
		$this->paths = $paths;
	}


	/**
	 * Calls the view helper with the given name and arguments and returns it's output.
	 *
	 * @param string $name Name of the view helper
	 * @param array $args Arguments passed to the view helper
	 * @return mixed Output depending on the view helper
	 */
	public function __call( $name, array $args )
	{
		if( !isset( $this->helper[$name] ) )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? '\\Aimeos\\MW\\View\\Helper\\' . $name : '<not a string>';
				throw new \Aimeos\MW\View\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
			}

			$iface = '\\Aimeos\\MW\\View\\Helper\\Iface';
			$classname = '\\Aimeos\\MW\\View\\Helper\\' . ucfirst( $name ) . '\\Standard';

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\MW\View\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$helper = new $classname( $this );

			if( !( $helper instanceof $iface ) ) {
				throw new \Aimeos\MW\View\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
			}

			$this->helper[$name] = $helper;
		}

		return call_user_func_array( array( $this->helper[$name], 'transform' ), $args );
	}


	/**
	 * Clones internal objects of the view.
	 */
	public function __clone()
	{
		foreach( $this->helper as $name => $helper )
		{
			$helper = clone $helper;

			// reset view so view helpers will use the current one (for translation, etc.)
			$helper->setView( $this );

			$this->helper[$name] = $helper;
		}
	}


	/**
	 * Returns the value associated to the given key.
	 *
	 * @param string $key Name of the value that should be returned
	 * @return mixed Value associated to the given key
	 * @throws \Aimeos\MW\View\Exception If the requested key isn't available
	 */
	public function __get( $key )
	{
		if( !isset( $this->values[$key] ) ) {
			throw new \Aimeos\MW\View\Exception( sprintf( 'No value for key "%1$s" found', $key ) );
		}

		return $this->values[$key];
	}


	/**
	 * Tests if a key with the given name exists.
	 *
	 * @param string $key Name of the value that should be tested
	 * @return boolean True if the key exists, false if not
	 */
	public function __isset( $key )
	{
		return isset( $this->values[$key] );
	}


	/**
	 * Removes a key from the stored values.
	 *
	 * @param string $key Name of the value that should be removed
	 */
	public function __unset( $key )
	{
		unset( $this->values[$key] );
	}


	/**
	 * Sets a new value for the given key.
	 *
	 * @param string $key Name of the value that should be set
	 * @param mixed $value Value associated to the given key
	 */
	public function __set( $key, $value )
	{
		$this->values[$key] = $value;
	}


	/**
	 * Adds a view helper instance to the view.
	 *
	 * @param string $name Name of the view helper as called in the template
	 * @param \Aimeos\MW\View\Helper\Iface $helper View helper instance
	 * @return \Aimeos\MW\View\Iface View object for method chaining
	 */
	public function addHelper( $name, \Aimeos\MW\View\Helper\Iface $helper )
	{
		$this->helper[$name] = $helper;
		return $this;
	}


	/**
	 * Assigns a whole set of values at once to the view.
	 * This method overwrites already existing key/value pairs set by the magic method.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function assign( array $values )
	{
		$this->values = $values;
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
		$values = $this->values;

		foreach( explode( '/', ltrim( $key, '/' ) ) as $part )
		{
			if( is_array( $values ) && isset( $values[$part] ) ) {
				$values = $values[$part];
			} else {
				return $default;
			}
		}

		return $values;
	}


	/**
	 * Renders the output based on the given template file name and the key/value pairs.
	 *
	 * @param string|array $filename File name of list of file names for the view templates
	 * @return string Output generated by the template
	 * @throws \Aimeos\MW\View\Exception If the template isn't found
	 */
	public function render( $filename )
	{
		$filepath = $this->resolve( $filename );

		foreach( $this->engines as $fileext => $engine )
		{
			if( substr_compare( $filepath, $fileext, -strlen( $fileext ) ) ===0 ) {
				return $engine->render( $this, $filepath, $this->values );
			}
		}

		try
		{
			ob_start();

			$this->includeFile( $filepath );

			return ob_get_clean();
		}
		catch( \Exception $e )
		{
			ob_end_clean();
			throw $e;
		}
	}


	/**
	 * Includes the template file and processes the PHP instructions.
	 * The filename is passed as first argument but without variable name to prevent messing the variable scope.
	 */
	protected function includeFile()
	{
		include func_get_arg( 0 );
	}


	/**
	 * Returns the absolute file name for the given relative one
	 *
	 * @param string|array $files File name of list of file names for the view templates
	 * @return string Absolute path to the template file
	 * @throws \Aimeos\MW\Exception If the template couldn't be found
	 */
	protected function resolve( $files )
	{
		foreach( (array) $files as $file )
		{
			if( is_file( $file ) ) {
				return $file;
			}

			$ds = DIRECTORY_SEPARATOR;

			foreach( array_reverse( $this->paths ) as $path => $relPaths )
			{
				foreach( $relPaths as $relPath )
				{
					$absPath = $path . $ds . $relPath . $ds . $file;
					if( $ds !== '/' ) {
						$absPath = str_replace( '/', $ds, $absPath );
					}

					if( is_file( $absPath ) ) {
						return $absPath;
					}
				}
			}
		}

		throw new \Aimeos\MW\View\Exception( sprintf( 'Template "%1$s" not available', $file ) );
	}
}
