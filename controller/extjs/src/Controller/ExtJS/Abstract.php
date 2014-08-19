<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * Common methods for ExtJS controller classes.
 *
 * @package Controller
 * @subpackage ExtJS
 */
abstract class Controller_ExtJS_Abstract
{
	private $_name = '';
	private $_sort = null;
	private $_context = null;


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 * @param string $name Name of the manager/item the controller is responsible for
	 * @param string|null $sort Attribute code used for default sortation
	 */
	public function __construct( MShop_Context_Item_Interface $context, $name, $sort = null )
	{
		$this->_context = $context;
		$this->_name = $name;
		$this->_sort = $sort;
	}


	/**
	 * Executes tasks before processing the items.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function init( stdClass $params )
	{
		return array(
			'success' => true,
		);
	}


	/**
	 * Executes tasks after processing the items.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function finish( stdClass $params )
	{
		return array(
			'success' => true,
		);
	}


	/**
	 * Deletes an item or a list of items.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function deleteItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		foreach( (array) $params->items as $id ) {
			$this->_getManager()->deleteItem( $id );
		}

		$this->_clearCache( (array) $params->items );

		return array(
			'items' => $params->items,
			'success' => true,
		);
	}


	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties, total number of items and success property
	 */
	public function searchItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site' ) );
		$this->_setLocale( $params->site );

		$total = 0;
		$search = $this->_initCriteria( $this->_getManager()->createSearch(), $params );
		$items = $this->_getManager()->searchItems( $search, array(), $total );

		return array(
			'items' => $this->_toArray( $items ),
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
			$this->_name . '.init' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			$this->_name . '.finish' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			$this->_name . '.deleteItems' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			$this->_name . '.saveItems' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			$this->_name . '.searchItems' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "condition","optional" => true ),
					array( "type" => "integer","name" => "start","optional" => true ),
					array( "type" => "integer","name" => "limit","optional" => true ),
					array( "type" => "string","name" => "sort","optional" => true ),
					array( "type" => "string","name" => "dir","optional" => true ),
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
		$attributes = $this->_getManager()->getSearchAttributes( false );
		return array(
			'name' => $this->_name,
			'properties' => $this->_getAttributeSchema( $attributes ),
		);
	}


	/**
	 * Returns the schema of the available search criteria and operators.
	 *
	 * @return array Associative list of "criteria" list (including "description", "type" and "optional") and "operators" list (including "compare", "combine" and "sort")
	 */
	public function getSearchSchema()
	{
		$attributes = $this->_getManager()->getSearchAttributes();

		return array(
			'criteria' => $this->_getAttributeSchema( $attributes, false ),
		);
	}


	/**
	 * Template method for returning the manager object used by the controller.
	 * This method has to be implemented in the derived classes
	 */
	abstract protected function _getManager();


	/**
	 * Checks if the uploaded file is valid.
	 *
	 * @param string $filename Name of the uploaded file in the file system of the server
	 * @param integer $errcode Status code of the uploaded file
	 * @throws Controller_ExtJS_Exception If file upload is invalid
	 */
	protected function _checkFileUpload( $filename, $errcode )
	{
		switch( $errcode )
		{
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new Controller_ExtJS_Exception( 'The uploaded file exceeds the max. allowed filesize' );
			case UPLOAD_ERR_PARTIAL:
				throw new Controller_ExtJS_Exception( 'The uploaded file was only partially uploaded' );
			case UPLOAD_ERR_NO_FILE:
				throw new Controller_ExtJS_Exception( 'No file was uploaded' );
			case UPLOAD_ERR_NO_TMP_DIR:
				throw new Controller_ExtJS_Exception( 'Temporary folder is missing' );
			case UPLOAD_ERR_CANT_WRITE:
				throw new Controller_ExtJS_Exception( 'Failed to write file to disk' );
			case UPLOAD_ERR_EXTENSION:
				throw new Controller_ExtJS_Exception( 'File upload stopped by extension' );
			default:
				throw new Controller_ExtJS_Exception( 'Unknown upload error' );
		}

		if( is_uploaded_file( $filename ) === false ) {
			throw new Controller_ExtJS_Exception( 'File was not uploaded' );
		}
	}


	/**
	 * Checks if the required parameter are available.
	 *
	 * @param stdClass $params Item object containing the parameter
	 * @param string[] $names List of names of the required parameter
	 * @throws Controller_ExtJS_Exception if a required parameter is missing
	 */
	protected function _checkParams( stdClass $params, array $names )
	{
		foreach( $names as $name )
		{
			if( !property_exists( $params, $name ) ) {
				throw new Controller_ExtJS_Exception( sprintf( 'Missing parameter "%1$s"', $name ), -1 );
			}
		}
	}


	/**
	 * Removes the cache entries tagged with the domain or domain items.
	 *
	 * @param array $ids List of domain IDs
	 */
	protected function _clearCache( array $ids )
	{
		$domain = str_replace( '_', '/', strtolower( $this->_name ) );
		$tags = array( $domain );

		foreach( $ids as $id ) {
			$tags[] = $domain . '-' . $id;
		}

		$this->_context->getCache()->deleteByTags( $tags );
	}


	/**
	 * Returns the item properties suitable for creating a JSON schema.
	 *
	 * @param array $attributes List of attribute object implementing MW_Common_Criteria_Attribute_Interface
	 * @param boolean $all True if all search attributes should be returned or false for only public ones
	 * @throws Controller_ExtJS_Exception if list item doesn't implement MW_Common_Criteria_Attribute_Interface
	 */
	protected function _getAttributeSchema( array $attributes, $all = true )
	{
		$properties = array();
		$iface = 'MW_Common_Criteria_Attribute_Interface';

		foreach( $attributes as $attribute )
		{
			if( !( $attribute instanceof $iface ) ) {
				throw new Controller_ExtJS_Exception( sprintf( 'List item doesn\'t implement "%1$s"', $iface ) );
			}

			if( $attribute->isPublic() || (bool) $all )
			{
				$properties[ $attribute->getCode() ] = array(
					'description' => $attribute->getLabel(),
					'optional' => !$attribute->isRequired(),
					'type' => $attribute->getType(),
				);
			}
		}
		return $properties;
	}


	/**
	 * Initializes the criteria object based on the given parameter.
	 *
	 * @param MW_Common_Criteria_Interface $criteria Criteria object
	 * @param stdClass $params Object that may contain the properties "condition", "sort", "dir", "start" and "limit"
	 * @return MW_Common_Criteria_Interface Initialized criteria object
	 */
	protected function _initCriteria( MW_Common_Criteria_Interface $criteria, stdClass $params )
	{
		$this->_initCriteriaConditions( $criteria, $params );
		$this->_initCriteriaSortations( $criteria, $params );
		$this->_initCriteriaSlice( $criteria, $params );

		return $criteria;
	}


	/**
	 * Initializes the criteria object with conditions based on the given parameter.
	 *
	 * @param MW_Common_Criteria_Interface $criteria Criteria object
	 * @param stdClass $params Object that may contain the properties "condition", "sort", "dir", "start" and "limit"
	 */
	private function _initCriteriaConditions( MW_Common_Criteria_Interface $criteria, stdClass $params )
	{
		if( isset( $params->condition ) && is_object( $params->condition ) )
		{
			$existing = $criteria->getConditions();
			$criteria->setConditions( $criteria->toConditions( (array) $params->condition ) );
			$expr = array (
				$criteria->getConditions(),
				$existing
			);

			$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		}
	}


	/**
	 * Initializes the criteria object with the slice based on the given parameter.
	 *
	 * @param MW_Common_Criteria_Interface $criteria Criteria object
	 * @param stdClass $params Object that may contain the properties "condition", "sort", "dir", "start" and "limit"
	 */
	private function _initCriteriaSlice( MW_Common_Criteria_Interface $criteria, stdClass $params )
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
	 * @param MW_Common_Criteria_Interface $criteria Criteria object
	 * @param stdClass $params Object that may contain the properties "condition", "sort", "dir", "start" and "limit"
	 */
	private function _initCriteriaSortations( MW_Common_Criteria_Interface $criteria, stdClass $params )
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
					throw new Controller_ExtJS_Exception( sprintf( 'Invalid sort direction "%1$s"', $params->sort ) );
			}

			$criteria->setSortations( $sortation );
		}


		if( $this->_sort !== null )
		{
			$sort = $criteria->getSortations();
			$sort[] = $criteria->sort( '+', $this->_sort );
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
	protected function _setLocale( $site, $langid = null, $currencyid = null )
	{
		$siteManager = MShop_Locale_Manager_Factory::createManager( $this->_context )->getSubManager( 'site' );

		$search = $siteManager->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', $site ) );
		$sites = $siteManager->searchItems( $search );

		if ( ( $siteItem = reset( $sites ) ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Site item for code "%1$s" not found', $site ) );
		}

		$values = array( 'siteid' => $siteItem->getId() );
		$sitepath = array_keys( $siteManager->getPath( $siteItem->getId() ) );
		$sitetree = $this->_getSiteIdsFromTree( $siteManager->getTree( $siteItem->getId() ) );

		$localeItem = new MShop_Locale_Item_Default( $values, $siteItem, $sitepath, $sitetree );
		$localeItem->setLanguageId( $langid );
		$localeItem->setCurrencyId( $currencyid );

		$this->_context->setLocale( $localeItem );
	}


	/**
	 * Converts the given list of objects to a list of stdClass objects
	 *
	 * @param array $list List of item objects
	 * @return array List of stdClass objects containing the properties of the item objects
	 */
	protected function _toArray( array $list )
	{
		$result = array();

		foreach( $list as $item ) {
			$result[] = (object) $item->toArray();
		}

		return $result;
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
	protected function _getDomainItems( array $lists )
	{
		$result = array();

		foreach( $lists as $domain => $ids )
		{
			$manager = MShop_Factory::createManager( $this->_context, $domain );

			$total = 0;
			$criteria = $manager->createSearch();
			$criteria->setConditions( $criteria->compare( '==', str_replace( '/', '.', $domain ) . '.id', $ids ) );

			$items = $manager->searchItems( $criteria, array(), $total );

			$parts = explode( '/', $domain );
			foreach( $parts as $key => $part ) {
				$parts[$key] = ucwords( $part );
			}

			$result[ implode( '_', $parts ) ] = array(
				'items' => $this->_toArray( $items ),
				'total' => $total,
			);
		}

		return $result;
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
	 * Returns the list of site IDs of the whole tree.
	 *
	 * @param MShop_Locale_Item_Site_Interface $item Locale item, maybe with children
	 * @return array List of site IDs
	 */
	private function _getSiteIdsFromTree( MShop_Locale_Item_Site_Interface $item )
	{
		$list = array( $item->getId() );

		foreach( $item->getChildren() as $child ) {
			$list = array_merge( $list, $this->_getSiteIdsFromTree( $child ) );
		}

		return $list;
	}
}
