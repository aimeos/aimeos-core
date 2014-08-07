<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS attribute controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Attribute_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the attribute controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Attribute' );

		$this->_manager = MShop_Attribute_Manager_Factory::createManager( $context );
	}


	/**
	 * Creates a new attribute item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the attribute properties
	 * @return array Associative list with items and success value
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

			if( isset( $entry->{'attribute.id'} ) ) { $item->setId( $entry->{'attribute.id'} ); }
			if( isset( $entry->{'attribute.typeid'} ) ) { $item->setTypeId( $entry->{'attribute.typeid'} ); }
			if( isset( $entry->{'attribute.domain'} ) ) { $item->setDomain( $entry->{'attribute.domain'} ); }
			if( isset( $entry->{'attribute.code'} ) ) { $item->setCode( $entry->{'attribute.code'} ); }
			if( isset( $entry->{'attribute.label'} ) ) { $item->setLabel( $entry->{'attribute.label'} ); }
			if( isset( $entry->{'attribute.position'} ) ) { $item->setPosition( $entry->{'attribute.position'} ); }
			if( isset( $entry->{'attribute.status'} ) ) { $item->setStatus( $entry->{'attribute.status'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$this->_clearCache( $ids );

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
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

		$idList = array();
		$ids = (array) $params->items;
		$context = $this->_getContext();
		$manager = $this->_getManager();


		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.id', $ids ) );
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
				$search->compare( '==', $domain.'.list.domain', 'attribute' )
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
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}
