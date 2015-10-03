<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS order controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Order_Default
	extends Controller_ExtJS_Base
	implements Controller_ExtJS_Common_Interface
{
	private $manager = null;


	/**
	 * Initializes the Order controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Order' );
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = MShop_Factory::createManager( $this->getContext(), 'order' );
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
		return 'order';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function transformValues( stdClass $entry )
	{
		if( isset( $entry->{'order.datestart'} ) && $entry->{'order.datestart'} != '' ) {
			$entry->{'order.datestart'} = str_replace( 'T', ' ', $entry->{'order.datestart'} );
		} else {
			$entry->{'order.datestart'} = null;
		}

		if( isset( $entry->{'order.dateend'} ) && $entry->{'order.dateend'} != '' ) {
			$entry->{'order.dateend'} = str_replace( 'T', ' ', $entry->{'order.dateend'} );
		} else {
			$entry->{'order.dateend'} = null;
		}

		return $entry;
	}
}
