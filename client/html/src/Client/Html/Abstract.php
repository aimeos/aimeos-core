<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Common abstract class for all HTML client classes.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Client_Html_Abstract
	implements Client_Html_Common_Client_Factory_Interface
{
	/**
	 * @deprecated Not used as caching is done internally
	 * @todo 2015.03 Remove constants
	 */
	const CACHE_BODY = 1;
	/**
	 * @deprecated Not used as caching is done internally
	 * @todo 2015.03 Remove constants
	 */
	const CACHE_HEADER = 2;

	private $_tags;
	private $_view;
	private $_clients;
	private $_context;
	private $_subclients;
	private $_templatePaths;
	private $_hashes = array();


	/**
	 * Initializes the class instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $templatePaths )
	{
		$this->_context = $context;
		$this->_templatePaths = $templatePaths;
	}


	/**
	 * Returns the view object that will generate the HTML output.
	 *
	 * @return MW_View_Interface $view The view object which generates the HTML output
	 */
	public function getView()
	{
		if( !isset( $this->_view ) ) {
			throw new Client_Html_Exception( sprintf( 'No view available' ) );
		}

		return $this->_view;
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 * @deprecated Not used anymore, caching is done internally
	 * @todo 2015.03 Remove method from API
	 */
	public function isCachable( $what )
	{
		return false;
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 *
	 * @param string $confpath Path to the configuration that contains the configured sub-clients
	 * @param array $default List of sub-client names that should be used if no other configuration is available
	 * @return boolean False if processing is stopped, otherwise all processing was completed successfully
	 */
	public function process()
	{
		$view = $this->getView();

		foreach( $this->_getSubClients() as $subclient )
		{
			$subclient->setView( $view );

			if( $subclient->process() === false ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Sets the view object that will generate the HTML output.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return Client_Html_Interface Reference to this object for fluent calls
	 */
	public function setView( MW_View_Interface $view )
	{
		$this->_view = $view;
		return $this;
	}


	/**
	 * Adds the cache tags to the given list and sets a new expiration date if necessary based on the given item.
	 *
	 * @param MShop_Common_Item_Interface $item Item, maybe with associated list items
	 * @param string $domain Name of the domain the item is from
	 * @param array $domains List of domains whose items are associated via the list to the item
	 * @param array &$tags List of tags the new tags will be added to
	 * @param string|null &$expire Expiration date that will be overwritten if an earlier date is found
	 */
	protected function _addMetaData( MShop_Common_Item_Interface $item, $domain, array $domains, array &$tags, &$expire )
	{
		$expires = array();
		$tags[] = $domain;

		if( method_exists( $item, 'getDateEnd' ) && ( $date = $item->getDateEnd() ) !== null ) {
			$expires[] = $date;
		}

		foreach( $domains as $name )
		{
			foreach( $item->getListItems( $name ) as $listitem )
			{
				if( ( $date = $listitem->getDateEnd() ) !== null ) {
					$expires[] = $date;
				}
			}
		}

		if( !empty( $expires ) ) {
			$expire = min( $expires );
		}
	}


	/**
	 * Transforms the client path to the appropriate class names.
	 *
	 * @param string $client Path of client names, e.g. "catalog/navigation"
	 * @return string Class names, e.g. "Catalog_Navigation"
	 */
	protected function _createSubNames( $client )
	{
		$names = explode( '/', $client );

		foreach( $names as $key => $subname )
		{
			if( empty( $subname ) || ctype_alnum( $subname ) === false ) {
				throw new Client_Html_Exception( sprintf( 'Invalid characters in client name "%1$s"', $client ) );
			}

			$names[$key] = ucfirst( $subname );
		}

		return implode( '_', $names );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $client Name of the sub-part in lower case (can contain a path like catalog/navigation)
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return Client_Html_Interface Sub-part object
	 */
	protected function _createSubClient( $client, $name )
	{
		$client = strtolower( $client );

		if( $name === null )
		{
			$path = 'client/html/' . $client . '/name';
			$name = $this->_context->getConfig()->get( $path, 'Default' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Invalid characters in client name "%1$s"', $name ) );
		}

		$subnames = $this->_createSubNames( $client );

		$classname = 'Client_Html_'. $subnames . '_' . $name;
		$interface = 'Client_Html_Interface';

		if( class_exists( $classname ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$subClient = new $classname( $this->_context, $this->_templatePaths );

		if( ( $subClient instanceof $interface ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $subClient;
	}


	/**
	 * Returns the parameters used by the html client.
	 *
	 * @param array $params Associative list of all parameters
	 * @param array $prefixes List of prefixes the parameters must start with
	 * @return array Associative list of parameters used by the html client
	 */
	protected function _getClientParams( array $params, array $prefixes = array( 'f', 'l', 'd', 'a' ) )
	{
		$list = array();

		foreach( $params as $key => $value )
		{
			if( in_array( $key[0], $prefixes ) && $key[1] === '-' ) {
				$list[$key] = $value;
			}
		}

		return $list;
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _getContext()
	{
		return $this->_context;
	}


	/**
	 * Generates an unique hash from based on the input suitable to be used as part of the cache key
	 *
	 * @param array $prefixes List of prefixes the parameters must start with
	 * @param string $key Unique identifier if the content is placed more than once on the same page
	 * @return string Unique hash
	 */
	protected function _getParamHash( array $prefixes = array( 'f', 'l', 'd' ), $key = '' )
	{
		$idx = implode( $prefixes ) . '/' . $key;

		if( !isset( $this->_hashes[$idx] ) )
		{
			$params = $this->_getClientParams( $this->getView()->param(), $prefixes );
			ksort( $params );

			foreach( $params as $name => $value )
			{
				if( is_array( $value ) ) {
					$value = implode( $value );
				}

				if( $value !== '' ) {
					$key .= $name . $value;
				}
			}

			$this->_hashes[$idx] = md5( $key );
		}

		return $this->_hashes[$idx];
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 * @todo 2015.03 Make abstract so clients have to implement it
	 */
	protected function _getSubClientNames()
	{
		return array();
	}


	/**
	 * Returns the configured sub-clients or the ones named in the default parameter if none are configured.
	 *
	 * @param string|null $confpath Path to the configuration that contains the configured sub-clients
	 * @param array $default List of sub-client names that should be used if no other configuration is available
	 * @return array List of sub-clients implementing Client_Html_Interface	ordered in the same way as the names
	 * @todo 2015.03 Remove $confpath and $default parameters
	 */
	protected function _getSubClients( $confpath = null , array $default = array() )
	{
		if( !isset( $this->_subclients ) )
		{
			$this->_subclients = array();

			if( $confpath !== null ) {
				$names = $this->_context->getConfig()->get( $confpath, $default );
			} else {
				$names = $this->_getSubClientNames();
			}

			foreach( $names as $name ) {
				$this->_subclients[] = $this->getSubClient( $name );
			}
		}

		return $this->_subclients;
	}


	/**
	 * Returns the absolute path to the given template file.
	 * It uses the first one found from the configured paths in the manifest files, but in reverse order.
	 *
	 * @param string $file Relative file path segments and its name separated by slashes
	 * @param string|array $default Relative file name or list of file names to use when nothing else is configured
	 * @return Absolute path the to the template file
	 * @throws Client_Html_Exception If no template file was found
	 */
	protected function _getTemplate( $confpath, $default )
	{
		$ds = DIRECTORY_SEPARATOR;

		foreach( (array) $default as $fname )
		{
			$file = $this->_context->getConfig()->get( $confpath, $fname );

			foreach( array_reverse( $this->_templatePaths ) as $path => $relPaths )
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

		throw new Client_Html_Exception( sprintf( 'Template "%1$s" not available', $file ) );
	}


	/**
	 * Tests if the output of the sub-clients is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @param string $confpath Path to the configuration that contains the configured sub-clients
	 * @param array $default List of sub-client names that should be used if no other configuration is available
	 * @return boolean True if the output can be cached, false if not
	 * @deprecated Not used anymore, caching is done internally
	 * @todo 2015.03 Remove method from API
	 */
	protected function _isCachable( $what, $confpath, array $default )
	{
		foreach( $this->_getSubClients( $confpath, $default ) as $subclient )
		{
			if( $subclient->isCachable( $what ) === false ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 *
	 * @param string $confpath Path to the configuration that contains the configured sub-clients
	 * @param array $default List of sub-client names that should be used if no other configuration is available
	 * @return boolean False if processing is stopped, otherwise all processing was completed successfully
	 * @deprecated Implement _getSubClientNames() to use process() from abstract class instead
	 * @todo 2015.03 Remove method from API
	 */
	protected function _process( $confpath, array $default )
	{
		$view = $this->getView();

		foreach( $this->_getSubClients( $confpath, $default ) as $subclient )
		{
			$subclient->setView( $view );

			if( $subclient->process() === false ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return MW_View_Interface Modified view object
	 * @todo 2015.03 Add $tags and $expire parameters
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		return $view;
	}


	/**
	 * Translates the plugin error codes to human readable error strings.
	 *
	 * @param array $codes Associative list of scope and object as key and error code as value
	 * @return array List of translated error messages
	 */
	protected function _translatePluginErrorCodes( array $codes )
	{
		$errors = array();
		$i18n = $this->_context->getI18n();

		foreach( $codes as $scope => $list )
		{
			foreach( $list as $object => $errcode )
			{
				$key = $scope . ( $scope !== 'product' ? '.' . $object : '' ) . '.' . $errcode;
				$errors[] = $i18n->dt( 'mshop/code', $key );
			}
		}

		return $errors;
	}
}
