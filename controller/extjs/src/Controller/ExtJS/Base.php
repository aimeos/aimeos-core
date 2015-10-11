<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS;


/**
 * Common methods for ExtJS controller classes.
 *
 * @package Controller
 * @subpackage ExtJS
 */
abstract class Base
{
	private $name = '';
	private $sort = null;
	private $context = null;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param string $name Name of the manager/item the controller is responsible for
	 * @param string|null $sort Attribute code used for default sortation
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, $name, $sort = null )
	{
		$this->context = $context;
		$this->name = $name;
		$this->sort = $sort;
	}


	/**
	 * Executes tasks before processing the items.
	 *
	 * @param \stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function init( \stdClass $params )
	{
		return array(
			'success' => true,
		);
	}


	/**
	 * Executes tasks after processing the items.
	 *
	 * @param \stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function finish( \stdClass $params )
	{
		return array(
			'success' => true,
		);
	}


	/**
	 * Deletes an item or a list of items.
	 *
	 * @param \stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function deleteItems( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );

		$this->getManager()->deleteItems( (array) $params->items );
		$this->clearCache( (array) $params->items );

		return array(
			'items' => $params->items,
			'success' => true,
		);
	}


	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param \stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties, total number of items and success property
	 */
	public function searchItems( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site' ) );
		$this->setLocale( $params->site );

		$total = 0;
		$manager = $this->getManager();
		$search = $this->initCriteria( $manager->createSearch(), $params );
		$items = $manager->searchItems( $search, array(), $total );

		return array(
			'items' => $this->toArray( $items ),
			'total' => $total,
			'success' => true,
		);
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		return array(
			$this->name . '.init' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			$this->name . '.finish' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			$this->name . '.deleteItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			$this->name . '.saveItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			$this->name . '.searchItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "condition", "optional" => true ),
					array( "type" => "integer", "name" => "start", "optional" => true ),
					array( "type" => "integer", "name" => "limit", "optional" => true ),
					array( "type" => "string", "name" => "sort", "optional" => true ),
					array( "type" => "string", "name" => "dir", "optional" => true ),
					array( "type" => "array", "name" => "options", "optional" => true ),
				),
				"returns" => "array",
			),
		);
	}


	/**
	 * Returns the schema of the item.
	 *
	 * @return array Associative list of "name" and "properties" list (including "description", "type" and "optional")
	 */
	public function getItemSchema()
	{
		$attributes = $this->getManager()->getSearchAttributes( false );
		return array(
			'name' => $this->name,
			'properties' => $this->getAttributeSchema( $attributes ),
		);
	}


	/**
	 * Returns the schema of the available search criteria and operators.
	 *
	 * @return array Associative list of "criteria" list (including "description", "type" and "optional") and "operators" list (including "compare", "combine" and "sort")
	 */
	public function getSearchSchema()
	{
		$attributes = $this->getManager()->getSearchAttributes();

		return array(
			'criteria' => $this->getAttributeSchema( $attributes, false ),
		);
	}


	/**
	 * Creates a new item or updates an existing one or a list thereof
	 *
	 * @param \stdClass $params Associative array containing the item properties
	 * @return array Associative array including items and status for ExtJS
	 */
	public function saveItems( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );

		$ids = array();
		$manager = $this->getManager();
		$entries = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $entries as $entry )
		{
			$item = $manager->createItem();
			$item->fromArray( (array) $this->transformValues( $entry ) );

			$manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		$this->clearCache( $ids );

		return $this->getItems( $ids, $this->getPrefix() );
	}


	/**
	 * Template method for returning the manager object used by the controller.
	 * This method has to be implemented in the derived classes
	 */
	abstract protected function getManager();


	/**
	 * Template method for returning the search key prefix of the used manager
	 * This method has to be implemented in the derived classes
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	abstract protected function getPrefix();


	/**
	 * Checks if the uploaded file is valid.
	 *
	 * @param string $filename Name of the uploaded file in the file system of the server
	 * @param integer $errcode Status code of the uploaded file
	 * @throws \Aimeos\Controller\ExtJS\Exception If file upload is invalid
	 */
	protected function checkFileUpload( $filename, $errcode )
	{
		switch( $errcode )
		{
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new \Aimeos\Controller\ExtJS\Exception( 'The uploaded file exceeds the max. allowed filesize' );
			case UPLOAD_ERR_PARTIAL:
				throw new \Aimeos\Controller\ExtJS\Exception( 'The uploaded file was only partially uploaded' );
			case UPLOAD_ERR_NO_FILE:
				throw new \Aimeos\Controller\ExtJS\Exception( 'No file was uploaded' );
			case UPLOAD_ERR_NO_TMP_DIR:
				throw new \Aimeos\Controller\ExtJS\Exception( 'Temporary folder is missing' );
			case UPLOAD_ERR_CANT_WRITE:
				throw new \Aimeos\Controller\ExtJS\Exception( 'Failed to write file to disk' );
			case UPLOAD_ERR_EXTENSION:
				throw new \Aimeos\Controller\ExtJS\Exception( 'File upload stopped by extension' );
			default:
				throw new \Aimeos\Controller\ExtJS\Exception( 'Unknown upload error' );
		}

		if( is_uploaded_file( $filename ) === false ) {
			throw new \Aimeos\Controller\ExtJS\Exception( 'File was not uploaded' );
		}
	}


	/**
	 * Checks if the required parameter are available.
	 *
	 * @param \stdClass $params Item object containing the parameter
	 * @param string[] $names List of names of the required parameter
	 * @throws \Aimeos\Controller\ExtJS\Exception if a required parameter is missing
	 */
	protected function checkParams( \stdClass $params, array $names )
	{
		foreach( $names as $name )
		{
			if( !property_exists( $params, $name ) ) {
				throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Missing parameter "%1$s"', $name ), -1 );
			}
		}
	}


	/**
	 * Removes the cache entries tagged with the domain or domain items.
	 *
	 * @param array $ids List of domain IDs
	 * @param string|null $domain Domain of the IDs, null for current domain
	 */
	protected function clearCache( array $ids, $domain = null )
	{
		$domain = ( $domain !== null ? $domain : strtolower( $this->name ) );
		$tags = array( $domain );

		foreach( $ids as $id ) {
			$tags[] = $domain . '-' . $id;
		}

		$this->context->getCache()->deleteByTags( $tags );
	}


	/**
	 * Returns the item properties suitable for creating a JSON schema.
	 *
	 * @param array $attributes List of attribute object implementing \Aimeos\MW\Common\Criteria\Attribute\Iface
	 * @param boolean $all True if all search attributes should be returned or false for only public ones
	 * @throws \Aimeos\Controller\ExtJS\Exception if list item doesn't implement \Aimeos\MW\Common\Criteria\Attribute\Iface
	 */
	protected function getAttributeSchema( array $attributes, $all = true )
	{
		$properties = array();
		$iface = '\\Aimeos\\MW\\Common\\Criteria\\Attribute\\Iface';

		foreach( $attributes as $attribute )
		{
			if( !( $attribute instanceof $iface ) ) {
				throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'List item doesn\'t implement "%1$s"', $iface ) );
			}

			if( $attribute->isPublic() || (bool) $all )
			{
				$properties[$attribute->getCode()] = array(
					'description' => $attribute->getLabel(),
					'optional' => !$attribute->isRequired(),
					'type' => $attribute->getType(),
				);
			}
		}
		return $properties;
	}


	/**
	 * Returns the items for the given domain and IDs
	 *
	 * @param array $ids List of domain item IDs
	 * @param string $prefix Search key prefix
	 * @return array Associative array including items and status for ExtJS
	 */
	protected function getItems( array $ids, $prefix )
	{
		$search = $this->getManager()->createSearch();
		$search->setConditions( $search->compare( '==', $prefix . '.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->toArray( $this->getManager()->searchItems( $search ) );

		return array(
			'items' => ( count( $ids ) === 1 ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Initializes the criteria object based on the given parameter.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $criteria Criteria object
	 * @param \stdClass $params Object that may contain the properties "condition", "sort", "dir", "start" and "limit"
	 * @return \Aimeos\MW\Common\Criteria\Iface Initialized criteria object
	 */
	protected function initCriteria( \Aimeos\MW\Common\Criteria\Iface $criteria, \stdClass $params )
	{
		$this->initCriteriaConditions( $criteria, $params );
		$this->initCriteriaSortations( $criteria, $params );
		$this->initCriteriaSlice( $criteria, $params );

		return $criteria;
	}


	/**
	 * Initializes the criteria object with conditions based on the given parameter.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $criteria Criteria object
	 * @param \stdClass $params Object that may contain the properties "condition", "sort", "dir", "start" and "limit"
	 */
	private function initCriteriaConditions( \Aimeos\MW\Common\Criteria\Iface $criteria, \stdClass $params )
	{
		if( isset( $params->condition ) && is_object( $params->condition ) )
		{
			$existing = $criteria->getConditions();
			$criteria->setConditions( $criteria->toConditions( (array) $params->condition ) );
			$expr = array(
				$criteria->getConditions(),
				$existing
			);

			$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		}
	}


	/**
	 * Initializes the criteria object with the slice based on the given parameter.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $criteria Criteria object
	 * @param \stdClass $params Object that may contain the properties "condition", "sort", "dir", "start" and "limit"
	 */
	private function initCriteriaSlice( \Aimeos\MW\Common\Criteria\Iface $criteria, \stdClass $params )
	{
		if( isset( $params->start ) && isset( $params->limit ) )
		{
			$start = ( isset( $params->start ) ? $params->start : 0 );
			$size = ( isset( $params->limit ) ? $params->limit : 25 );

			$criteria->setSlice( $start, $size );
		}
	}


	/**
	 * Initializes the criteria object with sortations based on the given parameter.
	 *
	 * @param \Aimeos\MW\Common\Criteria\Iface $criteria Criteria object
	 * @param \stdClass $params Object that may contain the properties "condition", "sort", "dir", "start" and "limit"
	 */
	private function initCriteriaSortations( \Aimeos\MW\Common\Criteria\Iface $criteria, \stdClass $params )
	{
		if( isset( $params->sort ) && isset( $params->dir ) )
		{
			$sortation = array();

			switch( $params->dir )
			{
				case 'ASC':
					$sortation[] = $criteria->sort( '+', $params->sort ); break;
				case 'DESC':
					$sortation[] = $criteria->sort( '-', $params->sort ); break;
				default:
					throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Invalid sort direction "%1$s"', $params->sort ) );
			}

			$criteria->setSortations( $sortation );
		}


		if( $this->sort !== null )
		{
			$sort = $criteria->getSortations();
			$sort[] = $criteria->sort( '+', $this->sort );
			$criteria->setSortations( $sort );
		}
	}


	/**
	 * Creates a new locale object and adds this object to the context.
	 *
	 * @param string $site Site code
	 * @param string|null $langid Two letter ISO code for language
	 * @param string|null $currencyid Three letter ISO code for currency
	 */
	protected function setLocale( $site, $langid = null, $currencyid = null )
	{
		$siteManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $this->context )->getSubManager( 'site' );

		$search = $siteManager->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', $site ) );
		$sites = $siteManager->searchItems( $search );

		if( ( $siteItem = reset( $sites ) ) === false ) {
			throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Site item for code "%1$s" not found', $site ) );
		}

		$values = array( 'siteid' => $siteItem->getId() );
		$sitepath = array_keys( $siteManager->getPath( $siteItem->getId() ) );
		$sitetree = $this->getSiteIdsFromTree( $siteManager->getTree( $siteItem->getId() ) );

		$localeItem = new \Aimeos\MShop\Locale\Item\Standard( $values, $siteItem, $sitepath, $sitetree );
		$localeItem->setLanguageId( $langid );
		$localeItem->setCurrencyId( $currencyid );

		$this->context->setLocale( $localeItem );
	}


	/**
	 * Converts the given list of objects to a list of \stdClass objects
	 *
	 * @param array $list List of item objects
	 * @return array List of \stdClass objects containing the properties of the item objects
	 */
	protected function toArray( array $list )
	{
		$result = array();

		foreach( $list as $item ) {
			$result[] = (object) $item->toArray();
		}

		return $result;
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param \stdClass $entry Entry object from ExtJS
	 * @return \stdClass Modified object
	 */
	protected function transformValues( \stdClass $entry )
	{
		return $entry;
	}


	/**
	 * Returns the items from the domains identified by their IDs.
	 *
	 * @param array $lists Associative list of domain names and list of IDs. The domain names must be in lower case
	 * 	(e.g. "product" for product items or "product/list" for product list items). Sub-domains are separated by a
	 * 	slash (/).
	 * @return array Associative list of controller names (e.g. "Product_List", generated from "product/list") and a
	 * 	list of pairs. Each list of pairs contains the key "items" with the list of object properties and the key
	 * 	"total" with the total number of items that are available in the storage
	 */
	protected function getDomainItems( array $lists )
	{
		$result = array();

		foreach( $lists as $domain => $ids )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->context, $domain );

			$total = 0;
			$criteria = $manager->createSearch();
			$criteria->setConditions( $criteria->compare( '==', str_replace( '/', '.', $domain ) . '.id', $ids ) );

			$items = $manager->searchItems( $criteria, array(), $total );

			$parts = explode( '/', $domain );
			foreach( $parts as $key => $part ) {
				$parts[$key] = ucwords( $part );
			}

			$result[implode( '_', $parts )] = array(
				'items' => $this->toArray( $items ),
				'total' => $total,
			);
		}

		return $result;
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
	 * Returns the list of site IDs of the whole tree.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $item Locale item, maybe with children
	 * @return array List of site IDs
	 */
	private function getSiteIdsFromTree( \Aimeos\MShop\Locale\Item\Site\Iface $item )
	{
		$list = array( $item->getId() );

		foreach( $item->getChildren() as $child ) {
			$list = array_merge( $list, $this->getSiteIdsFromTree( $child ) );
		}

		return $list;
	}
}
