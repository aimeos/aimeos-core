<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jsonapi
 */


namespace Aimeos\Controller\Jsonapi;


/**
 * JSON API controller interface
 *
 * @package Controller
 * @subpackage Jsonapi
 */
class Standard
	extends \Aimeos\Controller\Jsonapi\Base
	implements \Aimeos\Controller\Jsonapi\Common\Iface
{
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
		$view = $context->getView();

		try
		{
			$manager = \Aimeos\MShop\Factory::createManager( $context, $this->getPath() );

			if( ( $id = $view->param( 'id' ) ) === null )
			{
				if( ( $request = json_decode( $body ) ) === null || !isset( $request->data ) || !is_array( $request->data ) ) {
					throw new \Aimeos\Controller\Jsonapi\Exception( sprintf( 'Invalid JSON in body' ), 400 );
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
		catch( \Aimeos\Controller\Jsonapi\Exception $e )
		{
			$status = $e->getCode();
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
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
				'title' => $context->getI18n()->dt( 'controller/jsonapi', 'A non-recoverable error occured' ),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** controller/jsonapi/standard/template-delete
		 * Relative path to the JSON API template for DELETE requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in controller/jsonapi/templates).
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
		 * @see controller/jsonapi/standard/template-get
		 * @see controller/jsonapi/standard/template-patch
		 * @see controller/jsonapi/standard/template-post
		 * @see controller/jsonapi/standard/template-put
		 * @see controller/jsonapi/standard/template-options
		 */
		$tplconf = 'controller/jsonapi/standard/template-delete';
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
		$view = $context->getView();
		$total = 1;

		try
		{
			$manager = \Aimeos\MShop\Factory::createManager( $context, $this->getPath() );
			$domains = ( ( $include = $view->param( 'include' ) ) !== null ? explode( ',', $include ) : array() );

			if( ( $id = $view->param( 'id' ) ) === null )
			{
				$search = $this->initCriteria( $manager->createSearch(), $view->param() );
				$view->data = $manager->searchItems( $search, $domains, $total );
			}
			else
			{
				$view->data = $manager->getItem( $id, $domains );
			}

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
		catch( \Aimeos\MW\Exception $e )
		{
			$status = 500;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'controller/jsonapi', 'A non-recoverable error occured' ),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** controller/jsonapi/standard/template-get
		 * Relative path to the JSON API template for GET requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in controller/jsonapi/templates).
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
		 * @see controller/jsonapi/standard/template-delete
		 * @see controller/jsonapi/standard/template-patch
		 * @see controller/jsonapi/standard/template-post
		 * @see controller/jsonapi/standard/template-put
		 * @see controller/jsonapi/standard/template-options
		 */
		$tplconf = 'controller/jsonapi/standard/template-get';
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
		$view = $context->getView();

		try
		{
			if( ( $request = json_decode( $body ) ) === null || !isset( $request->data ) ) {
				throw new \Aimeos\Controller\Jsonapi\Exception( sprintf( 'Invalid JSON in body' ), 400 );
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
				if( isset( $request->data->attributes ) && isset( $request->data->id ) )
				{
					$item = $manager->getItem( $request->data->id );
					$item->fromArray( (array) $request->data->attributes );

					$manager->saveItem( $item );
					$data = $manager->getItem( $request->data->id );
				}

				$view->data = $data;
				$view->total = 1;
			}

			$status = 200;
		}
		catch( \Aimeos\Controller\Jsonapi\Exception $e )
		{
			$status = $e->getCode();
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
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
				'title' => $context->getI18n()->dt( 'controller/jsonapi', 'A non-recoverable error occured' ),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** controller/jsonapi/standard/template-patch
		 * Relative path to the JSON API template for PATCH requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in controller/jsonapi/templates).
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
		 * @see controller/jsonapi/standard/template-get
		 * @see controller/jsonapi/standard/template-post
		 * @see controller/jsonapi/standard/template-delete
		 * @see controller/jsonapi/standard/template-put
		 * @see controller/jsonapi/standard/template-options
		 */
		$tplconf = 'controller/jsonapi/standard/template-patch';
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
		$view = $context->getView();

		try
		{
			if( ( $request = json_decode( $body ) ) === null || !isset( $request->data ) ) {
				throw new \Aimeos\Controller\Jsonapi\Exception( sprintf( 'Invalid JSON in body' ), 400 );
			}

			if( isset( $request->data->id ) || $view->param( 'id' ) !== null ) {
				throw new \Aimeos\Controller\Jsonapi\Exception( sprintf( 'Client generated IDs are not supported' ), 403 );
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
		catch( \Aimeos\Controller\Jsonapi\Exception $e )
		{
			$status = $e->getCode();
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
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
				'title' => $context->getI18n()->dt( 'controller/jsonapi', 'A non-recoverable error occured' ),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** controller/jsonapi/standard/template-post
		 * Relative path to the JSON API template for POST requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in controller/jsonapi/templates).
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
		 * @see controller/jsonapi/standard/template-get
		 * @see controller/jsonapi/standard/template-patch
		 * @see controller/jsonapi/standard/template-delete
		 * @see controller/jsonapi/standard/template-put
		 * @see controller/jsonapi/standard/template-options
		 */
		$tplconf = 'controller/jsonapi/standard/template-post';
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
		$view = $context->getView();

		$view->errors = array( array(
			'title' => $context->getI18n()->dt( 'controller/jsonapi', 'Not implemented' ),
		) );

		/** controller/jsonapi/standard/template-put
		 * Relative path to the JSON API template for PUT requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in controller/jsonapi/templates).
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
		 * @see controller/jsonapi/standard/template-delete
		 * @see controller/jsonapi/standard/template-patch
		 * @see controller/jsonapi/standard/template-post
		 * @see controller/jsonapi/standard/template-get
		 * @see controller/jsonapi/standard/template-options
		 */
		$tplconf = 'controller/jsonapi/standard/template-put';
		$default = 'put-standard.php';

		return $view->render( $this->getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the available REST verbs
	 *
	 * @param string $body Request body
	 * @param string &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function options( $body, array &$header, &$status )
	{
		$header = array( 'Allow'=> 'DELETE,GET,POST,OPTIONS' );
		$status = 200;

		return '';
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
}
