<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @package MShop
 * @subpackage Coupon
 * @version $Id: Factory.php 37 2012-08-08 17:37:40Z fblasel $
 */


/**
 * Factory for coupon manager.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Manager_Factory
	extends MShop_Common_Factory_Abstract
	implements MShop_Common_Factory_Interface
{
	/**
	 * Creates an coupon manager DAO object.
	 *
	 * @param MShop_Context_Item_Interface $context Shop context instance with necessary objects
	 * @param string $name Manager name
	 * @return MShop_Common_Manager_Interface Manager object
	 * @throws MShop_Coupon_Exception|MShop_Exception If requested manager
	 * implementation couldn't be found or initialisation fails
	 */
	public static function createManager( MShop_Context_Item_Interface $context, $name = null )
	{
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/coupon/manager/name', 'Default');
		}

		if ( ctype_alnum($name) === false ) {
			throw new MShop_Coupon_Exception(sprintf('Invalid class name "%1$s"', $name));
		}

		$iface = 'MShop_Coupon_Manager_Interface';
		$classname = 'MShop_Coupon_Manager_' . $name;

		$manager = self::_createManager( $context, $classname, $iface );
		return self::_addManagerDecorators( $context, $manager, 'coupon' );
	}
}