<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS customer address controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Customer_Address_Default
	extends Controller_ExtJS_Base
	implements Controller_ExtJS_Common_Iface
{
	private $manager = null;


	/**
	 * Initializes the customer address controller.
	 *
	 * @param MShop_Context_Item_Iface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		parent::__construct( $context, 'Customer_Address' );
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Iface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = MShop_Factory::createManager( $this->getContext(), 'customer/address' );
		}

		return $this->manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MShop search key prefix
	 */
	protected function getPrefix()
	{
		return 'customer.address';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function transformValues( stdClass $entry )
	{
		if( isset( $entry->{'customer.address.languageid'} ) && $entry->{'customer.address.languageid'} === '' ) {
			$entry->{'customer.address.languageid'} = null;
		}

		if( isset( $entry->{'customer.address.countryid'} ) && $entry->{'customer.address.countryid'} === '' ) {
			$entry->{'customer.address.countryid'} = null;
		}

		return $entry;
	}
}
