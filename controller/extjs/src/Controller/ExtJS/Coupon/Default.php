<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJs coupon controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Coupon_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the coupon controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Coupon' );

		$this->_manager = MShop_Coupon_Manager_Factory::createManager( $context );
	}


	/**
	 * Creates a new coupon item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the coupon properties
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

			if ( isset($entry->{'coupon.id'}) ) { $item->setId($entry->{'coupon.id'});}
			if ( isset($entry->{'coupon.label'}) ) { $item->setLabel($entry->{'coupon.label'}); }
			if ( isset($entry->{'coupon.provider'}) ) { $item->setProvider($entry->{'coupon.provider'}); }
			if ( isset($entry->{'coupon.datestart'}) ) { $item->setDateStart($entry->{'coupon.datestart'}); }
			if ( isset($entry->{'coupon.dateend'}) ) { $item->setDateEnd($entry->{'coupon.dateend'} ); }
			if ( isset($entry->{'coupon.status'}) ) { $item->setStatus($entry->{'coupon.status'});}
			if ( isset($entry->{'coupon.config'}) ) { $item->setConfig( (array) $entry->{'coupon.config'}); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$this->_clearCache( $ids );

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.id', $ids ) );
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
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}