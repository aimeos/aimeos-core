<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS order base controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Order_Base_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the order base controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Order_Base' );

		$manager = MShop_Order_Manager_Factory::createManager( $context );
		$this->_manager = $manager->getSubManager( 'base' );
	}


	/**
	 * Creates a new order base item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the order base properties
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );

		$ids = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$langid = ( isset( $entry->{'order.base.languageid'} ) ? $entry->{'order.base.languageid'} : null );
			$currencyid = ( isset( $entry->{'order.base.currencyid'} ) ? $entry->{'order.base.currencyid'} : null );

			$this->_setLocale( $params->site, $langid, $currencyid );

			$item = $this->_manager->createItem();

			if( isset( $entry->{'order.base.id'} ) ) { $item->setId( $entry->{'order.base.id'} ); }
			if( isset( $entry->{'order.base.customerid'} ) ) { $item->setCustomerId( $entry->{'order.base.customerid'} ); }
			if( isset( $entry->{'order.base.comment'} ) ) { $item->setComment( $entry->{'order.base.comment'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
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
