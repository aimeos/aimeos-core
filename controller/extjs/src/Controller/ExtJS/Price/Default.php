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
			$item = $this->_manager->createItem();

			if( isset( $entry->{'price.id'} ) ) { $item->setId( $entry->{'price.id'} ); }
			if( isset( $entry->{'price.typeid'} ) ) { $item->setTypeId( $entry->{'price.typeid'} ); }
			if( isset( $entry->{'price.currencyid'} ) ) { $item->setCurrencyId( $entry->{'price.currencyid'} ); }
			if( isset( $entry->{'price.domain'} ) ) { $item->setDomain( $entry->{'price.domain'} ); }
			if( isset( $entry->{'price.label'} ) ) { $item->setLabel( $entry->{'price.label'} ); }
			if( isset( $entry->{'price.quantity'} ) ) { $item->setQuantity( $entry->{'price.quantity'} ); }
			if( isset( $entry->{'price.value'} ) ) { $item->setValue( $entry->{'price.value'} ); }
			if( isset( $entry->{'price.shipping'} ) ) { $item->setShipping( $entry->{'price.shipping'} ); }
			if( isset( $entry->{'price.rebate'} ) ) { $item->setRebate( $entry->{'price.rebate'} ); }
			if( isset( $entry->{'price.taxrate'} ) ) { $item->setTaxRate( $entry->{'price.taxrate'} ); }
			if( isset( $entry->{'price.status'} ) ) { $item->setStatus( $entry->{'price.status'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

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
		$this->_checkParams( $params, array( 'site' ) );
		$this->_setLocale( $params->site );

		$total = 0;
		$search = $this->_initCriteria( $this->_getManager()->createSearch(), $params );

		if( isset( $params->domain ) && isset( $params->parentid ) )
		{
			$manager = $this->_getDomainManager( $params->domain );
			$listManager = $manager->getSubManager( 'list' );
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
		$manager = $this->_getManager();

		foreach( (array) $params->items as $id )
		{
			$idList[ $manager->getItem( $id )->getDomain() ][] = $id;
			$manager->deleteItem( $id );
		}

		foreach( $idList as $manager => $ids )
		{
			$refDomainListManager = $this->_getDomainManager( $manager )->getSubManager('list');
			$search = $refDomainListManager->createSearch();
			$expr = array(
				$search->compare( '==', $manager.'.list.refid', $ids ),
				$search->compare( '==', $manager.'.list.domain', 'price' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$start = 0;

			do
			{
				$result = $refDomainListManager->searchItems( $search );

				foreach ( $result as $item ) {
					$refDomainListManager->deleteItem( $item->getId() );
				}

				$count = count( $result );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count > 0 );
		}

		return array(
				'items' => $params->items,
				'success' => true,
		);
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return mixed Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}
