<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
	implements Client_Html_Interface
{
	private $view;
	private $cache;
	private $context;
	private $subclients;
	private $templatePaths;


	/**
	 * Initializes the class instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $templatePaths )
	{
		$this->context = $context;
		$this->templatePaths = $templatePaths;
	}


	/**
	 * Returns the view object that will generate the HTML output.
	 *
	 * @return MW_View_Interface $view The view object which generates the HTML output
	 */
	public function getView()
	{
		if( !isset( $this->view ) ) {
			throw new Client_Html_Exception( sprintf( 'No view available' ) );
		}

		return $this->view;
	}


	/**
	 * Modifies the cached body content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified body content
	 */
	public function modifyBody( $content, $uid )
	{
		$view = $this->getView();

		foreach( $this->getSubClients() as $subclient )
		{
			$subclient->setView( $view );
			$content = $subclient->modifyBody( $content, $uid );
		}

		return $content;
	}


	/**
	 * Modifies the cached header content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified header content
	 */
	public function modifyHeader( $content, $uid )
	{
		$view = $this->getView();

		foreach( $this->getSubClients() as $subclient )
		{
			$subclient->setView( $view );
			$content = $subclient->modifyHeader( $content, $uid );
		}

		return $content;
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 *
	 * @return boolean False if processing is stopped, otherwise all processing was completed successfully
	 */
	public function process()
	{
		$view = $this->getView();

		foreach( $this->getSubClients() as $subclient )
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
		$this->view = $view;
		return $this;
	}


	/**
	 * Adds the decorators to the client object
	 *
	 * @param Client_Html_Interface $client Client object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param array $decorators List of decorator name that should be wrapped around the client
	 * @param string $classprefix Decorator class prefix, e.g. "Client_Html_Catalog_Decorator_"
	 * @return Client_Html_Interface Client object
	 */
	protected function addDecorators( Client_Html_Interface $client, array $templatePaths,
		array $decorators, $classprefix )
	{
		$iface = 'Client_Html_Common_Decorator_Interface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? $classprefix . $name : '<not a string>';
				throw new Client_Html_Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new Client_Html_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$client = new $classname( $this->context, $this->templatePaths, $client );

			if( !( $client instanceof $iface ) ) {
				throw new Client_Html_Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $iface ) );
			}
		}

		return $client;
	}


	/**
	 * Adds the decorators to the client object
	 *
	 * @param Client_Html_Interface $client Client object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Client string in lower case, e.g. "catalog/detail/basic"
	 * @return Client_Html_Interface Client object
	 */
	protected function addClientDecorators( Client_Html_Interface $client, array $templatePaths, $path )
	{
		if( !is_string( $path ) || $path === '' ) {
			throw new Client_Html_Exception( sprintf( 'Invalid domain "%1$s"', $path ) );
		}

		$localClass = str_replace( ' ', '_', ucwords( str_replace( '/', ' ', $path ) ) );
		$config = $this->context->getConfig();

		$decorators = $config->get( 'client/html/common/decorators/default', array() );
		$excludes = $config->get( 'client/html/' . $path . '/decorators/excludes', array() );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = 'Client_Html_Common_Decorator_';
		$client = $this->addDecorators( $client, $templatePaths, $decorators, $classprefix );

		$classprefix = 'Client_Html_Common_Decorator_';
		$decorators = $config->get( 'client/html/' . $path . '/decorators/global', array() );
		$client = $this->addDecorators( $client, $templatePaths, $decorators, $classprefix );

		$classprefix = 'Client_Html_' . $localClass . '_Decorator_';
		$decorators = $config->get( 'client/html/' . $path . '/decorators/local', array() );
		$client = $this->addDecorators( $client, $templatePaths, $decorators, $classprefix );

		return $client;
	}


	/**
	 * Adds the cache tags to the given list and sets a new expiration date if necessary based on the given item.
	 *
	 * @param array|MShop_Common_Item_Interface $items Item or list of items, maybe with associated list items
	 * @param string $domain Name of the domain the item is from
	 * @param string|null &$expire Expiration date that will be overwritten if an earlier date is found
	 * @param array &$tags List of tags the new tags will be added to
	 */
	protected function addMetaItem( $items, $domain, &$expire, array &$tags )
	{
		/** client/html/common/cache/tag-all
		 * Adds tags for all items used in a cache entry
		 *
		 * Each cache entry storing rendered parts for the HTML header or body
		 * can be tagged with information which items like texts, media, etc.
		 * are used in the HTML. This allows removing only those cache entries
		 * whose content has really changed and only that entries have to be
		 * rebuild the next time.
		 *
		 * The standard behavior stores only tags for each used domain, e.g. if
		 * a text is used, only the tag "text" is added. If you change a text
		 * in the administration interface, all cache entries with the tag
		 * "text" will be removed from the cache. This effectively wipes out
		 * almost all cached entries, which have to be rebuild with the next
		 * request.
		 *
		 * Important: As a list or detail view can use several hundred items,
		 * this configuration option will also add this number of tags to the
		 * cache entry. When using a cache adapter that can't insert all tags
		 * at once, this slows down the initial cache insert (and therefore the
		 * page speed) drastically! It's only recommended to enable this option
		 * if you use the DB, Mysql or Redis adapter that can insert all tags
		 * at once.
		 *
		 * @param boolean True to add tags for all items, false to use only a domain tag
		 * @since 2014.07
		 * @category Developer
		 * @category User
		 * @see client/html/common/cache/force
		 * @see classes/cache/manager/name
		 * @see classes/cache/name
		 */
		$tagAll = $this->context->getConfig()->get( 'client/html/common/cache/tag-all', false );

		if( !is_array( $items ) ) {
			$items = array( $items );
		}

		if( $tagAll !== true && !empty( $items ) ) {
			$tags[] = $domain;
		}

		foreach( $items as $item ) {
			$this->addMetaItemSingle( $item, $domain, $expire, $tags, $tagAll );
		}
	}


	/**
	 * Adds expire date and tags for a single item.
	 *
	 * @param MShop_Common_Item_Interface $item Item, maybe with associated list items
	 * @param string $domain Name of the domain the item is from
	 * @param string|null &$expire Expiration date that will be overwritten if an earlier date is found
	 * @param array &$tags List of tags the new tags will be added to
	 * @param boolean $tagAll True of tags for all items should be added, false if only for the main item
	 */
	private function addMetaItemSingle( MShop_Common_Item_Interface $item, $domain, &$expire, array &$tags, $tagAll )
	{
		$expires = array();
		$domain = str_replace( '/', '_', $domain ); // maximum compatiblity

		if( $tagAll === true ) {
			$tags[] = $domain . '-' . $item->getId();
		}

		if( $item instanceof MShop_Common_Item_Time_Interface && ( $date = $item->getDateEnd() ) !== null ) {
			$expires[] = $date;
		}

		if( $item instanceof MShop_Common_Item_ListRef_Interface )
		{
			foreach( $item->getListItems() as $listitem )
			{
				if( $tagAll === true ) {
					$tags[] = str_replace( '/', '_', $listitem->getDomain() ) . '-' . $listitem->getRefId();
				}

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
	 * Adds a new expiration date if a list item is activated in the future.
	 *
	 * @param array|string $ids Item ID or list of item IDs from the given domain
	 * @param string $domain Name of the domain the item IDs are from
	 * @param string|null &$expire Expiration date that will be overwritten if an start date in the future is available
	 */
	protected function addMetaList( $ids, $domain, &$expire )
	{
		$manager = MShop_Factory::createManager( $this->getContext(), $domain . '/list' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', $domain . '.list.parentid', $ids ),
			$search->compare( '>', $domain . '.list.datestart', date( 'Y-m-d H:i:00' ) ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', $domain . '.list.datestart' ) ) );
		$search->setSlice( 0, 1 );

		foreach( $manager->searchItems( $search ) as $listItem ) {
			$expire = $this->expires( $expire, $listItem->getDateStart() );
		}
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $path Name of the sub-part in lower case (can contain a path like catalog/filter/tree)
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return Client_Html_Interface Sub-part object
	 */
	protected function createSubClient( $path, $name )
	{
		$path = strtolower( $path );

		if( $name === null ) {
			$name = $this->context->getConfig()->get( 'client/html/' . $path . '/name', 'Default' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Invalid characters in client name "%1$s"', $name ) );
		}

		$subnames = str_replace( ' ', '_', ucwords( str_replace( '/', ' ', $path ) ) );

		$classname = 'Client_Html_' . $subnames . '_' . $name;
		$interface = 'Client_Html_Interface';

		if( class_exists( $classname ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$object = new $classname( $this->context, $this->templatePaths );

		if( ( $object instanceof $interface ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $this->addClientDecorators( $object, $this->templatePaths, $path );
	}


	/**
	 * Returns the minimal expiration date.
	 *
	 * @param string|null $first First expiration date or null
	 * @param string|null $second Second expiration date or null
	 * @return string|null Expiration date
	 */
	protected function expires( $first, $second )
	{
		return ( $first !== null ? ( $second !== null ? min( $first, $second ) : $first ) : $second );
	}


	/**
	 * Returns the parameters used by the html client.
	 *
	 * @param array $params Associative list of all parameters
	 * @param array $prefixes List of prefixes the parameters must start with
	 * @return array Associative list of parameters used by the html client
	 */
	protected function getClientParams( array $params, array $prefixes = array( 'f', 'l', 'd', 'a' ) )
	{
		$list = array();

		foreach( $params as $key => $value )
		{
			if( in_array( $key[0], $prefixes ) && $key[1] === '_' ) {
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
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Generates an unique hash from based on the input suitable to be used as part of the cache key
	 *
	 * @param array $prefixes List of prefixes the parameters must start with
	 * @param string $key Unique identifier if the content is placed more than once on the same page
	 * @param array $config Multi-dimensional array of configuration options used by the client and sub-clients
	 * @return string Unique hash
	 */
	protected function getParamHash( array $prefixes = array( 'f', 'l', 'd' ), $key = '', array $config = array() )
	{
		$locale = $this->getContext()->getLocale();
		$params = $this->getClientParams( $this->getView()->param(), $prefixes );
		ksort( $params );

		if( ( $pstr = json_encode( $params ) ) === false || ( $cstr = json_encode( $config ) ) === false ) {
			throw new Client_Html_Exception( 'Unable to encode parameters or configuration options' );
		}

		return md5( $key . $pstr . $cstr . $locale->getLanguageId() . $locale->getCurrencyId() );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	abstract protected function getSubClientNames();


	/**
	 * Returns the configured sub-clients or the ones named in the default parameter if none are configured.
	 *
	 * @param string|null $confpath Path to the configuration that contains the configured sub-clients
	 * @param array $default List of sub-client names that should be used if no other configuration is available
	 * @return array List of sub-clients implementing Client_Html_Interface	ordered in the same way as the names
	 * @todo 2015.03 Remove $confpath and $default parameters
	 */
	protected function getSubClients( $confpath = null, array $default = array() )
	{
		if( !isset( $this->subclients ) )
		{
			$this->subclients = array();

			if( $confpath !== null ) {
				$names = $this->context->getConfig()->get( $confpath, $default );
			} else {
				$names = $this->getSubClientNames();
			}

			foreach( $names as $name ) {
				$this->subclients[] = $this->getSubClient( $name );
			}
		}

		return $this->subclients;
	}


	/**
	 * Returns the absolute path to the given template file.
	 * It uses the first one found from the configured paths in the manifest files, but in reverse order.
	 *
	 * @param string|array $default Relative file name or list of file names to use when nothing else is configured
	 * @param string $confpath Configuration key of the path to the template file
	 * @return string path the to the template file
	 * @throws Client_Html_Exception If no template file was found
	 */
	protected function getTemplate( $confpath, $default )
	{
		$ds = DIRECTORY_SEPARATOR;

		foreach( (array) $default as $fname )
		{
			$file = $this->context->getConfig()->get( $confpath, $fname );

			foreach( array_reverse( $this->templatePaths ) as $path => $relPaths )
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
	 * Returns the paths where the layout templates can be found
	 *
	 * @return array List of template paths
	 * @since 2015.09
	 */
	protected function getTemplatePaths()
	{
		return $this->templatePaths;
	}


	/**
	 * Returns the attribute type item specified by the code.
	 *
	 * @param string $prefix Domain prefix for the manager, e.g. "media/type"
	 * @param string $domain Domain of the type item
	 * @param string $code Code of the type item
	 * @return MShop_Common_Item_Type_Interface Type item
	 * @throws Controller_Jobs_Exception If no item is found
	 */
	protected function getTypeItem( $prefix, $domain, $code )
	{
		$manager = MShop_Factory::createManager( $this->getContext(), $prefix );
		$prefix = str_replace( '/', '.', $prefix );

		$search = $manager->createSearch();
		$expr = array(
				$search->compare( '==', $prefix . '.domain', $domain ),
				$search->compare( '==', $prefix . '.code', $code ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false )
		{
			$msg = sprintf( 'No type item for "%1$s/%2$s" in "%3$s" found', $domain, $code, $prefix );
			throw new Controller_Jobs_Exception( $msg );
		}

		return $item;
	}


	/**
	 * Returns the cache entry for the given unique ID and type.
	 *
	 * @param string $type Type of the cache entry, i.e. "body" or "header"
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param string[] $prefixes List of prefixes of all parameters that are relevant for generating the output
	 * @param string $confkey Configuration key prefix that matches all relevant settings for the component
	 * @return string Cached entry or empty string if not available
	 */
	protected function getCached( $type, $uid, array $prefixes, $confkey )
	{
		if( !isset( $this->cache ) )
		{
			$context = $this->getContext();
			$config = $context->getConfig();

			/** client/html/common/cache/force
			 * Enforces content caching regardless of user logins
			 *
			 * Caching the component output is normally disabled as soon as the
			 * user has logged in. This enables displaying user or user group
			 * specific content without mixing standard and user specific output.
			 *
			 * If you don't have any user or user group specific content
			 * (products, categories, attributes, media, prices, texts, etc.),
			 * you can enforce content caching nevertheless to keep response
			 * times as low as possible.
			 *
			 * @param boolean True to cache output regardless of login, false for no caching
			 * @since 2015.08
			 * @category Developer
			 * @category User
			 * @see client/html/common/cache/tag-all
			 */
			$force = $config->get( 'client/html/common/cache/force', false );

			if( $force == false && $context->getUserId() !== null ) {
				return null;
			}

			$cfg = $config->get( $confkey, array() );

			$keys = array(
				'body' => $this->getParamHash( $prefixes, $uid . ':' . $confkey . ':body', $cfg ),
				'header' => $this->getParamHash( $prefixes, $uid . ':' . $confkey . ':header', $cfg ),
			);

			$entries = $context->getCache()->getList( $keys );
			$this->cache = array();

			foreach( $keys as $key => $hash ) {
				$this->cache[$key] = ( array_key_exists( $hash, $entries ) ? $entries[$hash] : null );
			}
		}

		return ( array_key_exists( $type, $this->cache ) ? $this->cache[$type] : null );
	}


	/**
	 * Returns the cache entry for the given type and unique ID.
	 *
	 * @param string $type Type of the cache entry, i.e. "body" or "header"
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param string[] $prefixes List of prefixes of all parameters that are relevant for generating the output
	 * @param string $confkey Configuration key prefix that matches all relevant settings for the component
	 * @param string $value Value string that should be stored for the given key
	 * @param array $tags List of tag strings that should be assoicated to the
	 * 	given value in the cache
	 * @param string|null $expire Date/time string in "YYYY-MM-DD HH:mm:ss"
	 * 	format when the cache entry expires
	 */
	protected function setCached( $type, $uid, array $prefixes, $confkey, $value, array $tags, $expire )
	{
		$context = $this->getContext();
		$config = $context->getConfig();

		$force = $config->get( 'client/html/common/cache/force', false );

		if( $force == false && $context->getUserId() !== null ) {
			return;
		}

		try
		{
			$cfg = $config->get( $confkey, array() );
			$key = $this->getParamHash( $prefixes, $uid . ':' . $confkey . ':' . $type, $cfg );

			$context->getCache()->set( $key, $value, array_unique( $tags ), $expire );
		}
		catch( Exception $e )
		{
			$msg = sprintf( 'Unable to set cache entry: %1$s', $e->getMessage() );
			$context->getLogger()->log( $msg, MW_Logger_Abstract::NOTICE );
		}
	}


	/**
	 * Replaces the section in the content that is enclosed by the marker.
	 *
	 * @param string $content Cached content
	 * @param string $section New section content
	 * @param string $marker Name of the section marker without "<!-- " and " -->" parts
	 */
	protected function replaceSection( $content, $section, $marker )
	{
		$marker = '<!-- ' . $marker . ' -->';
		$start = strpos( $content, $marker );

		if( $start !== false && ( $end = strpos( $content, $marker, $start + 1 ) ) !== false ) {
			return substr_replace( $content, $section, $start, $end - $start + strlen( $marker ) );
		}

		return $content;
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return MW_View_Interface Modified view object
	 */
	protected function setViewParams( MW_View_Interface $view, array &$tags = array(), &$expire = null )
	{
		return $view;
	}


	/**
	 * Translates the plugin error codes to human readable error strings.
	 *
	 * @param array $codes Associative list of scope and object as key and error code as value
	 * @return array List of translated error messages
	 */
	protected function translatePluginErrorCodes( array $codes )
	{
		$errors = array();
		$i18n = $this->context->getI18n();

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
