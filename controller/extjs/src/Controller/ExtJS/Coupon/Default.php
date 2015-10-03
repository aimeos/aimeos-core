<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015
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
	extends Controller_ExtJS_Base
	implements Controller_ExtJS_Common_Interface
{
	private $manager = null;


	/**
	 * Initializes the coupon controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Coupon' );
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = MShop_Factory::createManager( $this->getContext(), 'coupon' );
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
		return 'coupon';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function transformValues( stdClass $entry )
	{
		if( isset( $entry->{'coupon.datestart'} ) ) {
			$entry->{'coupon.datestart'} = str_replace( 'T', ' ', $entry->{'coupon.datestart'} );
		}

		if( isset( $entry->{'coupon.dateend'} ) ) {
			$entry->{'coupon.dateend'} = str_replace( 'T', ' ', $entry->{'coupon.dateend'} );
		}

		if( isset( $entry->{'coupon.config'} ) ) {
			$entry->{'coupon.config'} = (array) $entry->{'coupon.config'};
		}

		return $entry;
	}
}