<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm;


/**
 * Common abstract class for all admin client classes.
 *
 * @package Client
 * @subpackage JQAdm
 */
abstract class Base
	implements \Aimeos\Admin\JQAdm\Iface
{
	private $view;
	private $context;
	private $subclients;
	private $templatePaths;
	private $types = array();


	/**
	 * Initializes the class instance.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths )
	{
		$this->context = $context;
		$this->templatePaths = $templatePaths;
	}


	/**
	 * Returns the view object that will generate the admin output.
	 *
	 * @return \Aimeos\MW\View\Iface $view The view object which generates the admin output
	 */
	public function getView()
	{
		if( !isset( $this->view ) ) {
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'No view available' ) );
		}

		return $this->view;
	}


	/**
	 * Sets the view object that will generate the admin output.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the admin output
	 * @return \Aimeos\Admin\JQAdm\Iface Reference to this object for fluent calls
	 */
	public function setView( \Aimeos\MW\View\Iface $view )
	{
		$this->view = $view;
		return $this;
	}


	/**
	 * Deletes a resource
	 *
	 * @return string|null admin output to display or null for redirecting to the list
	 */
	public function delete()
	{
	}


	/**
	 * Returns a list of resource according to the conditions
	 *
	 * @return string admin output to display
	 */
	public function search()
	{
	}


	/**
	 * Adds the decorators to the client object
	 *
	 * @param \Aimeos\Admin\JQAdm\Iface $client Client object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param array $decorators List of decorator name that should be wrapped around the client
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\Admin\JQAdm\Catalog\Decorator\"
	 * @return \Aimeos\Admin\JQAdm\Iface Client object
	 */
	protected function addDecorators( \Aimeos\Admin\JQAdm\Iface $client, array $templatePaths,
		array $decorators, $classprefix )
	{
		$iface = '\\Aimeos\\Admin\\JQAdm\\Common\\Decorator\\Iface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? $classprefix . $name : '<not a string>';
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$client = new $classname( $client, $this->context, $this->templatePaths );

			if( !( $client instanceof $iface ) ) {
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $iface ) );
			}
		}

		return $client;
	}


	/**
	 * Adds the decorators to the client object
	 *
	 * @param \Aimeos\Admin\JQAdm\Iface $client Client object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Client string in lower case, e.g. "catalog/detail/basic"
	 * @return \Aimeos\Admin\JQAdm\Iface Client object
	 */
	protected function addClientDecorators( \Aimeos\Admin\JQAdm\Iface $client, array $templatePaths, $path )
	{
		if( !is_string( $path ) || $path === '' ) {
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Invalid domain "%1$s"', $path ) );
		}

		$localClass = str_replace( ' ', '\\', ucwords( str_replace( '/', ' ', $path ) ) );
		$config = $this->context->getConfig();

		$decorators = $config->get( 'admin/jqadm/common/decorators/default', array() );
		$excludes = $config->get( 'admin/jqadm/' . $path . '/decorators/excludes', array() );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\\Aimeos\\Admin\\JQAdm\\Common\\Decorator\\';
		$client = $this->addDecorators( $client, $templatePaths, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\Admin\\JQAdm\\Common\\Decorator\\';
		$decorators = $config->get( 'admin/jqadm/' . $path . '/decorators/global', array() );
		$client = $this->addDecorators( $client, $templatePaths, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\Admin\\JQAdm\\' . $localClass . '\\Decorator\\';
		$decorators = $config->get( 'admin/jqadm/' . $path . '/decorators/local', array() );
		$client = $this->addDecorators( $client, $templatePaths, $decorators, $classprefix );

		return $client;
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $path Name of the sub-part in lower case (can contain a path like catalog/filter/tree)
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\Admin\JQAdm\Iface Sub-part object
	 */
	protected function createSubClient( $path, $name )
	{
		$path = strtolower( $path );

		if( $name === null ) {
			$name = $this->context->getConfig()->get( 'admin/jqadm/' . $path . '/name', 'Standard' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Invalid characters in client name "%1$s"', $name ) );
		}

		$subnames = str_replace( ' ', '\\', ucwords( str_replace( '/', ' ', $path ) ) );

		$classname = '\\Aimeos\\Admin\\JQAdm\\' . $subnames . '\\' . $name;
		$interface = '\\Aimeos\\Admin\\JQAdm\\Iface';

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$object = new $classname( $this->context, $this->templatePaths );

		if( ( $object instanceof $interface ) === false ) {
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		$object = $this->addClientDecorators( $object, $this->templatePaths, $path );
		$object->setView( $this->view );

		return $object;
	}


	/**
	 * Returns the known client parameters and their values
	 *
	 * @param array $names List of parameter names
	 * @return array Associative list of parameters names as key and their values
	 */
	protected function getClientParams( $names = array( 'resource', 'site', 'lang', 'fields', 'filter', 'page', 'sort' ) )
	{
		$list = array();

		foreach( $names as $name )
		{
			if( ( $val = $this->view->param( $name ) ) !== null ) {
				$list[$name] = $val;
			}
		}

		return $list;
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of admin client names
	 */
	abstract protected function getSubClientNames();


	/**
	 * Returns the configured sub-clients or the ones named in the default parameter if none are configured.
	 *
	 * @return array List of sub-clients implementing \Aimeos\Admin\JQAdm\Iface ordered in the same way as the names
	 */
	protected function getSubClients()
	{
		if( !isset( $this->subclients ) )
		{
			$this->subclients = array();

			foreach( $this->getSubClientNames() as $name ) {
				$this->subclients[] = $this->getSubClient( $name );
			}
		}

		return $this->subclients;
	}


	/**
	 * Returns the paths where the layout templates can be found
	 *
	 * @return array List of template paths
	 */
	protected function getTemplatePaths()
	{
		return $this->templatePaths;
	}


	/**
	 * Initializes the criteria object based on the given parameter
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Criteria object
	 * @param array $params List of criteria data with condition, sorting and paging
	 * @return \Aimeos\MW\Criteria\Iface Initialized criteria object
	 */
	protected function initCriteria( \Aimeos\MW\Criteria\Iface $criteria, array $params )
	{
		$this->initCriteriaConditions( $criteria, $params );
		$this->initCriteriaSortations( $criteria, $params );
		$this->initCriteriaSlice( $criteria, $params );

		return $criteria;
	}


	/**
	 * Initializes the criteria object with conditions based on the given parameter
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Criteria object
	 * @param array $params List of criteria data with condition, sorting and paging
	 */
	private function initCriteriaConditions( \Aimeos\MW\Criteria\Iface $criteria, array $params )
	{
		if( isset( $params['filter'] ) && isset( $params['filter']['key'] ) )
		{
			$expr = array();
			$existing = $criteria->getConditions();

			foreach( (array) $params['filter']['key'] as $idx => $key )
			{
				if( $key != '' && isset( $params['filter']['op'][$idx] ) && $params['filter']['op'][$idx] != ''
					&& isset( $params['filter']['val'][$idx] )
				) {
					$expr[] = $criteria->compare( $params['filter']['op'][$idx], $key, $params['filter']['val'][$idx] );
				}
			}

			$expr[] = $existing;
			$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		}
	}


	/**
	 * Initializes the criteria object with the slice based on the given parameter.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Criteria object
	 * @param array $params List of criteria data with condition, sorting and paging
	 */
	private function initCriteriaSlice( \Aimeos\MW\Criteria\Iface $criteria, array $params )
	{
		$start = ( isset( $params['page']['offset'] ) ? $params['page']['offset'] : 0 );
		$size = ( isset( $params['page']['limit'] ) ? $params['page']['limit'] : 100 );

		$criteria->setSlice( $start, $size );
	}


	/**
	 * Initializes the criteria object with sortations based on the given parameter
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Criteria object
	 * @param array $params List of criteria data with condition, sorting and paging
	 */
	private function initCriteriaSortations( \Aimeos\MW\Criteria\Iface $criteria, array $params )
	{
		if( !isset( $params['sort'] ) ) {
			return;
		}

		$sortation = array();

		foreach( (array) $params['sort'] as $sort )
		{
			if( $sort[0] === '-' ) {
				$sortation[] = $criteria->sort( '-', substr( $sort, 1 ) );
			} else {
				$sortation[] = $criteria->sort( '+', $sort ); break;
			}
		}

		$criteria->setSortations( $sortation );
	}
}
