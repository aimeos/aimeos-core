<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS customer controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Customer_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the customer controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Customer' );
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		if( $this->_manager === null ) {
			$this->_manager = MShop_Factory::createManager( $this->_getContext(), 'customer' );
		}

		return $this->_manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MShop search key prefix
	 */
	protected function _getPrefix()
	{
		return 'customer';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function _transformValues( stdClass $entry )
	{
		if( isset( $entry->{'customer.birthday'} ) )
		{
			if( $entry->{'customer.birthday'} != '' ) {
				$entry->{'customer.birthday'} = substr( $entry->{'customer.birthday'}, 0, 10 );
			} else {
				$entry->{'customer.birthday'} = null;
			}
		}

		if( isset( $entry->{'customer.dateverified'} ) )
		{
			if( $entry->{'customer.dateverified'} != '' ) {
				$entry->{'customer.dateverified'} = substr( $entry->{'customer.dateverified'}, 0, 10 );
			} else {
				$entry->{'customer.dateverified'} = null;
			}
		}

		return $entry;
	}
}
