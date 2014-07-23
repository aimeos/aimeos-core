<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS order base address controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Order_Base_Address_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the Order base address controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Order_Base_Address' );

		$manager = MShop_Order_Manager_Factory::createManager( $context );
		$baseManager =  $manager->getSubManager( 'base' );
		$this->_manager = $baseManager->getSubManager( 'address' );
	}


	/**
	 * Creates a new order base address item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the order base address properties
	 * @return array Associative list with nodes and success value
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

			if( isset( $entry->{'order.base.address.baseid'} ) ) { $item->setBaseId( $entry->{'order.base.address.baseid'} ); }
			if( isset( $entry->{'order.base.address.addressid'} ) ) { $item->setAddressId( $entry->{'order.base.address.addressid'} ); }
			if( isset( $entry->{'order.base.address.type'} ) ) { $item->setType( $entry->{'order.base.address.type'} ); }

			if( isset( $entry->{'order.base.address.company'} ) ) { $item->setCompany( $entry->{'order.base.address.company'} ); }
			if( isset( $entry->{'order.base.address.vatno'} ) ) { $item->setVatNo( $entry->{'order.base.address.vatno'} ); }
			if( isset( $entry->{'order.base.address.salutation'} ) ) { $item->setSalutation( $entry->{'order.base.address.salutation'} ); }
			if( isset( $entry->{'order.base.address.title'} ) ) { $item->setTitle( $entry->{'order.base.address.title'} ); }
			if( isset( $entry->{'order.base.address.firstname'} ) ) { $item->setFirstname( $entry->{'order.base.address.firstname'} ); }
			if( isset( $entry->{'order.base.address.lastname'} ) ) { $item->setLastname( $entry->{'order.base.address.lastname'} ); }
			if( isset( $entry->{'order.base.address.address1'} ) ) { $item->setAddress1( $entry->{'order.base.address.address1'} ); }
			if( isset( $entry->{'order.base.address.address2'} ) ) { $item->setAddress2( $entry->{'order.base.address.address2'} ); }
			if( isset( $entry->{'order.base.address.address3'} ) ) { $item->setAddress3( $entry->{'order.base.address.address3'} ); }
			if( isset( $entry->{'order.base.address.postal'} ) ) { $item->setPostal( $entry->{'order.base.address.postal'} ); }
			if( isset( $entry->{'order.base.address.city'} ) ) { $item->setCity( $entry->{'order.base.address.city'} ); }
			if( isset( $entry->{'order.base.address.state'} ) ) { $item->setState( $entry->{'order.base.address.state'} ); }
			if( isset( $entry->{'order.base.address.countryid'} ) ) { $item->setCountryId( $entry->{'order.base.address.countryid'} ); }
			if( isset( $entry->{'order.base.address.languageid'} ) ) { $item->setLanguageId( $entry->{'order.base.address.languageid'} ); }
			if( isset( $entry->{'order.base.address.telephone'} ) ) { $item->setTelephone( $entry->{'order.base.address.telephone'} ); }
			if( isset( $entry->{'order.base.address.email'} ) ) { $item->setEmail( $entry->{'order.base.address.email'} ); }
			if( isset( $entry->{'order.base.address.telefax'} ) ) { $item->setTelefax( $entry->{'order.base.address.telefax'} ); }
			if( isset( $entry->{'order.base.address.website'} ) ) { $item->setWebsite( $entry->{'order.base.address.website'} ); }


			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.address.id', $ids ) );
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
