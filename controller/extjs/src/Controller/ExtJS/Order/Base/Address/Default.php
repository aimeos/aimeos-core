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
			$item = $this->_createItem( (array) $entry );
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
	 * Creates a new order.base.address item and sets the properties from the given array.
	 *
	 * @param array $entry Associative list of name and value properties using the "order.base.address" prefix
	 * @return MShop_Attribute_Item_Interface Attribute item
	 */
	protected function _createItem( array $entry )
	{
		$item = $this->_manager->createItem();

		foreach( $entry as $name => $value )
		{
			switch( $name )
			{
				case 'order.base.address.type': $item->setType( $value ); break;
				case 'order.base.address.baseid': $item->setBaseId( $value ); break;
				case 'order.base.address.addressid': $item->setAddressId( $value ); break;

				case 'order.base.address.vatid': $item->setVatID( $value ); break;
				case 'order.base.address.company': $item->setCompany( $value ); break;
				case 'order.base.address.salutation': $item->setSalutation( $value ); break;
				case 'order.base.address.title': $item->setTitle( $value ); break;
				case 'order.base.address.firstname': $item->setFirstname( $value ); break;
				case 'order.base.address.lastname': $item->setLastname( $value ); break;
				case 'order.base.address.address1': $item->setAddress1( $value ); break;
				case 'order.base.address.address2': $item->setAddress2( $value ); break;
				case 'order.base.address.address3': $item->setAddress3( $value ); break;
				case 'order.base.address.postal': $item->setPostal( $value ); break;
				case 'order.base.address.city': $item->setCity( $value ); break;
				case 'order.base.address.state': $item->setState( $value ); break;
				case 'order.base.address.countryid': $item->setCountryId( $value ); break;
				case 'order.base.address.languageid': $item->setLanguageId( $value ); break;
				case 'order.base.address.telephone': $item->setTelephone( $value ); break;
				case 'order.base.address.telefax': $item->setTelefax( $value ); break;
				case 'order.base.address.website': $item->setWebsite( $value ); break;
				case 'order.base.address.email': $item->setEmail( $value ); break;
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
