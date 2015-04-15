<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJs price controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Price_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the media controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Price' );

		$this->_manager = MShop_Price_Manager_Factory::createManager( $context );
	}


	/**
	 * Creates a new price item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the text properties
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$ids = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_createItem( (array) $entry );
			$this->_manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		$this->_clearCache( $ids );

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'price.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
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
		/** controller/extjs/price/show-all
		 * Display prices of all items of the same domain in the admin interface
		 *
		 * By default, only the prices for the specific product, attribute or
		 * services associated to that items are shown in the price list views.
		 * This reduces to probability to associate a price to multiple items
		 * by accident but also prevents shop owners to use this for conveniance.
		 *
		 * When you set this option to "1", all prices of the same domain will
		 * be listed, e.g. all product prices. You can filter this prices in the
		 * list view and search for prices with specific properties. If a price
		 * is associated to more than one product, attribute or service, it will
		 * change for all items at once when one of the price properties is
		 * adapted.
		 *
		 * @param boolean True or "1" to show all prices, false or "0" otherwise
		 * @category Developer
		 * @catefory User
		 * @since 2015.05
		 */
		$allprices = $this->_getContext()->getConfig()->get( 'controller/extjs/price/show-all', false );

		$this->_checkParams( $params, array( 'site' ) );
		$this->_setLocale( $params->site );

		$total = 0;
		$search = $this->_initCriteria( $this->_getManager()->createSearch(), $params );

		if( isset( $params->domain ) && isset( $params->parentid ) && $allprices == false )
		{
			$listManager = MShop_Factory::createManager( $this->_getContext(), $params->domain . '/list' );
			$criteria = $listManager->createSearch();

			$expr = array();
			$expr[] = $criteria->compare( '==', $params->domain . '.list.parentid', $params->parentid );
			$expr[] = $criteria->compare( '==', $params->domain . '.list.domain', 'price' );
			$criteria->setConditions( $criteria->combine( '&&', $expr ) );

			$result = $listManager->searchItems( $criteria );

			$ids = array();
			foreach( $result as $items ) {
				$ids[] = $items->getRefId();
			}

			$expr = array();
			$expr[] = $search->compare( '==', 'price.id', $ids );
			$expr[] = $search->getConditions();
			$search->setConditions( $search->combine( '&&', $expr ) );
		}

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
			'Price.deleteItems' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			'Price.saveItems' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			'Price.searchItems' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "condition","optional" => true ),
					array( "type" => "integer","name" => "start","optional" => true ),
					array( "type" => "integer","name" => "limit","optional" => true ),
					array( "type" => "string","name" => "sort","optional" => true ),
					array( "type" => "string","name" => "dir","optional" => true ),
					array( "type" => "string","name" => "domain","optional" => true ),
					array( "type" => "string","name" => "label","optional" => true ),
					array( "type" => "integer","name" => "parentid","optional" => true ),
				),
				"returns" => "array",
			),
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

		$idList = array();
		$ids = (array) $params->items;
		$context = $this->_getContext();
		$manager = $this->_getManager();


		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'price.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );

		foreach( $manager->searchItems( $search ) as $id => $item ) {
			$idList[ $item->getDomain() ][] = $id;
		}

		$manager->deleteItems( $ids );


		foreach( $idList as $domain => $domainIds )
		{
			$manager = MShop_Factory::createManager( $context, $domain . '/list' );

			$search = $manager->createSearch();
			$expr = array(
				$search->compare( '==', $domain.'.list.refid', $domainIds ),
				$search->compare( '==', $domain.'.list.domain', 'price' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', $domain.'.list.id' ) ) );

			$start = 0;

			do
			{
				$result = $manager->searchItems( $search );
				$manager->deleteItems( array_keys( $result ) );

				$count = count( $result );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count >= $search->getSliceSize() );
		}

		$this->_clearCache( $ids );

		return array(
				'items' => $params->items,
				'success' => true,
		);
	}


	/**
	 * Creates a new price item and sets the properties from the given array.
	 *
	 * @param array $entry Associative list of name and value properties using the "price" prefix
	 * @return MShop_Attribute_Item_Interface Attribute item
	 */
	protected function _createItem( array $entry )
	{
		$item = $this->_manager->createItem();

		foreach( $entry as $name => $value )
		{
			switch( $name )
			{
				case 'price.id': $item->setId( $value ); break;
				case 'price.label': $item->setLabel( $value ); break;
				case 'price.domain': $item->setDomain( $value ); break;
				case 'price.typeid': $item->setTypeId( $value ); break;
				case 'price.status': $item->setStatus( $value ); break;
				case 'price.value': $item->setValue( $value ); break;
				case 'price.costs': $item->setCosts( $value ); break;
				case 'price.rebate': $item->setRebate( $value ); break;
				case 'price.taxrate': $item->setTaxRate( $value ); break;
				case 'price.quantity': $item->setQuantity( $value ); break;
				case 'price.currencyid': $item->setCurrencyId( $value ); break;
			}
		}

		return $item;
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}
