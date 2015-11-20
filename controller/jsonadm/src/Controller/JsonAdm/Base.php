<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage JsonAdm
 */


namespace Aimeos\Controller\JsonAdm;


/**
 * JSON API common controller
 *
 * @package Controller
 * @subpackage JsonAdm
 */
class Base
{
	private $view;
	private $context;
	private $templatePaths;
	private $path;


	/**
	 * Initializes the controller
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the controller separated by slashes, e.g "product/stock"
	 * @return void
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MW\View\Iface $view, array $templatePaths, $path )
	{
		$this->view = $view;
		$this->context = $context;
		$this->templatePaths = $templatePaths;
		$this->path = $path;
	}


	/**
	 * Deletes the resource or the resource list
	 *
	 * @param string $body Request body
	 * @param string &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function delete( $body, array &$header, &$status )
	{
		$header = array( 'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"' );
		$context = $this->getContext();
		$view = $this->getView();

		try
		{
			$manager = \Aimeos\MShop\Factory::createManager( $context, $this->getPath() );

			if( ( $id = $view->param( 'id' ) ) == null )
			{
				if( ( $request = json_decode( $body ) ) === null || !isset( $request->data ) || !is_array( $request->data ) ) {
					throw new \Aimeos\Controller\JsonAdm\Exception( sprintf( 'Invalid JSON in body' ), 400 );
				}

				$ids = array();

				foreach( $request->data as $entry )
				{
					if( isset( $entry->id ) ) {
						$ids[] = $entry->id;
					}
				}

				$manager->deleteItems( $ids );
				$view->total = count( $ids );
			}
			else
			{
				$manager->deleteItem( $id );
				$view->total = 1;
			}

			$status = 200;
		}
		catch( \Aimeos\Controller\JsonAdm\Exception $e )
		{
			$status = $e->getCode();
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'controller/jsonadm', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MAdmin\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Exception $e )
		{
			$status = 500;
			$view->errors = array( array(
				'title' => $e->getMessage(),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** controller/jsonadm/standard/template-delete
		 * Relative path to the JSON API template for DELETE requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in controller/jsonadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the body for the DELETE method of the JSON API
		 * @since 2015.12
		 * @category Developer
		 * @see controller/jsonadm/standard/template-get
		 * @see controller/jsonadm/standard/template-patch
		 * @see controller/jsonadm/standard/template-post
		 * @see controller/jsonadm/standard/template-put
		 * @see controller/jsonadm/standard/template-options
		 */
		$tplconf = 'controller/jsonadm/standard/template-delete';
		$default = 'delete-standard.php';

		return $view->render( $this->getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the requested resource or the resource list
	 *
	 * @param string $body Request body
	 * @param string &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function get( $body, array &$header, &$status )
	{
		$header = array( 'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"' );
		$context = $this->getContext();
		$view = $this->getView();
		$total = 1;

		try
		{
			$manager = \Aimeos\MShop\Factory::createManager( $context, $this->getPath() );
			$include = ( ( $include = $view->param( 'include' ) ) !== null ? explode( ',', $include ) : array() );

			if( ( $id = $view->param( 'id' ) ) == null )
			{
				$search = $this->initCriteria( $manager->createSearch(), $view->param() );
				$view->data = $manager->searchItems( $search, array(), $total );
				$view->childItems = $this->getChildItems( $view->data, $include );
				$view->listItems = $this->getListItems( $view->data, $include );
			}
			else
			{
				$view->data = $manager->getItem( $id, array() );
				$view->childItems = $this->getChildItems( array( $id => $view->data ), $include );
				$view->listItems = $this->getListItems( array( $id => $view->data ), $include );
			}

			$view->refItems = $this->getRefItems( $view->listItems );

			$view->total = $total;
			$status = 200;
		}
		catch( \Aimeos\MAdmin\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Exception $e )
		{
			$status = 500;
			$view->errors = array( array(
				'title' => $e->getMessage(),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** controller/jsonadm/standard/template-get
		 * Relative path to the JSON API template for GET requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in controller/jsonadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the body for the GET method of the JSON API
		 * @since 2015.12
		 * @category Developer
		 * @see controller/jsonadm/standard/template-delete
		 * @see controller/jsonadm/standard/template-patch
		 * @see controller/jsonadm/standard/template-post
		 * @see controller/jsonadm/standard/template-put
		 * @see controller/jsonadm/standard/template-options
		 */
		$tplconf = 'controller/jsonadm/standard/template-get';
		$default = 'get-standard.php';

		return $view->render( $this->getTemplate( $tplconf, $default ) );
	}


	/**
	 * Updates the resource or the resource list partitially
	 *
	 * @param string $body Request body
	 * @param string &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function patch( $body, array &$header, &$status )
	{
		$header = array( 'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"' );
		$context = $this->getContext();
		$view = $this->getView();

		try
		{
			if( ( $request = json_decode( $body ) ) === null || !isset( $request->data ) ) {
				throw new \Aimeos\Controller\JsonAdm\Exception( sprintf( 'Invalid JSON in body' ), 400 );
			}

			$data = null;
			$manager = \Aimeos\MShop\Factory::createManager( $context, $this->getPath() );

			if( is_array( $request->data ) )
			{
				$data = array();

				foreach( $request->data as $entry )
				{
					if( isset( $entry->attributes ) && isset( $entry->id ) )
					{
						$item = $manager->getItem( $entry->id );
						$item->fromArray( (array) $entry->attributes );

						$manager->saveItem( $item );
						$data[] = $manager->getItem( $entry->id );
					}
				}

				$view->data = $data;
				$view->total = count( $data );
				$header['Content-Type'] = 'application/vnd.api+json; ext="bulk"; supported-ext="bulk"';
			}
			else
			{
				if( ( $id = $view->param( 'id' ) ) == null ) {
					throw new \Aimeos\Controller\JsonAdm\Exception( sprintf( 'No ID given' ), 400 );
				}

				if( isset( $request->data->attributes ) )
				{
					$item = $manager->getItem( $id );
					$item->fromArray( (array) $request->data->attributes );

					$manager->saveItem( $item );
					$data = $manager->getItem( $id );
				}

				$view->data = $data;
				$view->total = 1;
			}

			$status = 200;
		}
		catch( \Aimeos\Controller\JsonAdm\Exception $e )
		{
			$status = $e->getCode();
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'controller/jsonadm', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MAdmin\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MW\Exception $e )
		{
			$status = 500;
			$view->errors = array( array(
				'title' => $e->getMessage(),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** controller/jsonadm/standard/template-patch
		 * Relative path to the JSON API template for PATCH requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in controller/jsonadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the body for the PATCH method of the JSON API
		 * @since 2015.12
		 * @category Developer
		 * @see controller/jsonadm/standard/template-get
		 * @see controller/jsonadm/standard/template-post
		 * @see controller/jsonadm/standard/template-delete
		 * @see controller/jsonadm/standard/template-put
		 * @see controller/jsonadm/standard/template-options
		 */
		$tplconf = 'controller/jsonadm/standard/template-patch';
		$default = 'patch-standard.php';

		return $view->render( $this->getTemplate( $tplconf, $default ) );
	}


	/**
	 * Creates or updates the resource or the resource list
	 *
	 * @param string $body Request body
	 * @param string &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function post( $body, array &$header, &$status )
	{
		$header = array( 'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"' );
		$context = $this->getContext();
		$view = $this->getView();

		try
		{
			if( ( $request = json_decode( $body ) ) === null || !isset( $request->data ) ) {
				throw new \Aimeos\Controller\JsonAdm\Exception( sprintf( 'Invalid JSON in body' ), 400 );
			}

			if( isset( $request->data->id ) || $view->param( 'id' ) != null ) {
				throw new \Aimeos\Controller\JsonAdm\Exception( sprintf( 'Client generated IDs are not supported' ), 403 );
			}

			$data = null;
			$manager = \Aimeos\MShop\Factory::createManager( $context, $this->getPath() );

			if( is_array( $request->data ) )
			{
				$data = array();

				foreach( $request->data as $entry )
				{
					if( isset( $entry->attributes ) )
					{
						$item = $manager->createItem();
						$item->fromArray( (array) $entry->attributes );

						$manager->saveItem( $item );
						$data[] = $manager->getItem( $item->getId() );
					}
				}

				$view->data = $data;
				$view->total = count( $data );
				$header['Content-Type'] = 'application/vnd.api+json; ext="bulk"; supported-ext="bulk"';
			}
			else
			{
				if( isset( $request->data->attributes ) )
				{
					$item = $manager->createItem();
					$item->fromArray( (array) $request->data->attributes );

					$manager->saveItem( $item );
					$data = $manager->getItem( $item->getId() );
				}

				$view->data = $data;
				$view->total = 1;
			}

			$status = 201;
		}
		catch( \Aimeos\Controller\JsonAdm\Exception $e )
		{
			$status = $e->getCode();
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'controller/jsonadm', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MAdmin\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Exception $e )
		{
			$status = 500;
			$view->errors = array( array(
				'title' => $e->getMessage(),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** controller/jsonadm/standard/template-post
		 * Relative path to the JSON API template for POST requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in controller/jsonadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the body for the POST method of the JSON API
		 * @since 2015.12
		 * @category Developer
		 * @see controller/jsonadm/standard/template-get
		 * @see controller/jsonadm/standard/template-patch
		 * @see controller/jsonadm/standard/template-delete
		 * @see controller/jsonadm/standard/template-put
		 * @see controller/jsonadm/standard/template-options
		 */
		$tplconf = 'controller/jsonadm/standard/template-post';
		$default = 'post-standard.php';

		return $view->render( $this->getTemplate( $tplconf, $default ) );
	}


	/**
	 * Creates or updates the resource or the resource list
	 *
	 * @param string $body Request body
	 * @param string &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function put( $body, array &$header, &$status )
	{
		$header = array( 'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"' );
		$status = 501;

		$context = $this->getContext();
		$view = $this->getView();

		$view->errors = array( array(
			'title' => $context->getI18n()->dt( 'controller/jsonadm', 'Not implemented, use PATCH instead' ),
		) );

		/** controller/jsonadm/standard/template-put
		 * Relative path to the JSON API template for PUT requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in controller/jsonadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the body for the PUT method of the JSON API
		 * @since 2015.12
		 * @category Developer
		 * @see controller/jsonadm/standard/template-delete
		 * @see controller/jsonadm/standard/template-patch
		 * @see controller/jsonadm/standard/template-post
		 * @see controller/jsonadm/standard/template-get
		 * @see controller/jsonadm/standard/template-options
		*/
		$tplconf = 'controller/jsonadm/standard/template-put';
		$default = 'put-standard.php';

		return $view->render( $this->getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the available REST verbs and the available resources
	 *
	 * @param string $body Request body
	 * @param string &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function options( $body, array &$header, &$status )
	{
		$context = $this->getContext();
		$view = $this->getView();

		try
		{
			$resources = array();

			if( ( $domains = $view->param( 'resource' ) ) == '' )
			{
				$default = array(
					'attribute', 'catalog', 'coupon', 'customer', 'locale', 'media',
					'order', 'plugin', 'price', 'product', 'service', 'supplier', 'tag', 'text'
				);
				$domains = $context->getConfig()->get( 'controller/jsonadm/domains', $default );
			}

			foreach( (array) $domains as $domain )
			{
				$manager = \Aimeos\MShop\Factory::createManager( $context, $domain );
				$resources = array_merge( $resources, $manager->getResourceType( true ) );
			}

			$view->resources = $resources;

			$header = array(
				'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"',
				'Allow' => 'DELETE,GET,POST,OPTIONS'
			);
			$status = 200;
		}
		catch( \Aimeos\MAdmin\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Exception $e )
		{
			$status = 500;
			$view->errors = array( array(
				'title' => $e->getMessage(),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** controller/jsonadm/standard/template-options
		 * Relative path to the JSON API template for OPTIONS requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in controller/jsonadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the body for the OPTIONS method of the JSON API
		 * @since 2015.12
		 * @category Developer
		 * @see controller/jsonadm/standard/template-delete
		 * @see controller/jsonadm/standard/template-patch
		 * @see controller/jsonadm/standard/template-post
		 * @see controller/jsonadm/standard/template-get
		 * @see controller/jsonadm/standard/template-put
		*/
		$tplconf = 'controller/jsonadm/standard/template-options';
		$default = 'options-standard.php';

		return $view->render( $this->getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the view object
	 *
	 * @return \Aimeos\MW\View\Iface View object
	 */
	protected function getView()
	{
		return $this->view;
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
		if( isset( $params['filter'] ) && is_array( $params['filter'] ) )
		{
			$existing = $criteria->getConditions();
			$criteria->setConditions( $criteria->toConditions( (array) $params['filter'] ) );

			$expr = array( $criteria->getConditions(), $existing );
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
		$size = ( isset( $params['page']['limit'] ) ? $params['page']['limit'] : 25 );

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

		foreach( explode( ',', $params['sort'] ) as $sort )
		{
			if( $sort[0] === '-' ) {
				$sortation[] = $criteria->sort( '-', substr( $sort, 1 ) );
			} else {
				$sortation[] = $criteria->sort( '+', $sort ); break;
			}
		}

		$criteria->setSortations( $sortation );
	}


	/**
	 * Returns the items with parent/child relationships
	 *
	 * @param array $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	protected function getChildItems( array $items, array $include )
	{
		return array();
	}


	/**
	 * Returns the list items for association relationships
	 *
	 * @param array $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( array $items, array $include )
	{
		return array();
	}


	/**
	 * Returns the items associated via a lists table
	 *
	 * @param array $items List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	protected function getRefItems( array $listItems )
	{
		$list = $map = array();

		foreach( $listItems as $listItem ) {
			$map[$listItem->getDomain()][] = $listItem->getRefId();
		}

		foreach( $map as $domain => $ids )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $domain );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', $domain . '.id', $ids ) );

			$list = array_merge( $list, $manager->searchItems( $search ) );
		}

		return $list;
	}


	/**
	 * Returns the context item object
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the paths to the template files
	 *
	 * @return array List of file system paths
	 */
	protected function getTemplatePaths()
	{
		return $this->templatePaths;
	}


	/**
	 * Returns the path to the controller
	 *
	 * @return string Controller path, e.g. "product/stock"
	 */
	protected function getPath()
	{
		return $this->path;
	}


	/**
	 * Returns the absolute path to the given template file.
	 * It uses the first one found from the configured paths in the manifest files, but in reverse order.
	 *
	 * @param string|array $default Relative file name or list of file names to use when nothing else is configured
	 * @param string $confpath Configuration key of the path to the template file
	 * @return string path the to the template file
	 * @throws \Aimeos\Controller\JsonAdm\Exception If no template file was found
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

		throw new \Aimeos\Controller\JsonAdm\Exception( sprintf( 'Template "%1$s" not available', $file ) );
	}
}
