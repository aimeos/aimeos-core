<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @package MShop
 * @subpackage Coupon
 * @version $Id: Abstract.php 37 2012-08-08 17:37:40Z fblasel $
 */

/**
 * Abstract class for coupon managers.
 *
 * @package MShop
 * @subpackage Coupon
 */
abstract class MShop_Coupon_Manager_Abstract
	extends MShop_Common_Manager_Abstract
{
	protected function _addCouponDecorators( MShop_Coupon_Item_Interface $item, $code,
		MShop_Coupon_Provider_Interface $provider, $names, &$outer )
	{
		$iface = 'MShop_Coupon_Provider_Decorator_Interface';
		$classprefix = 'MShop_Coupon_Provider_Decorator_';

		foreach( $names as $name )
		{
			if ( ctype_alnum( $name ) === false ) {
				throw new MShop_Coupon_Exception( sprintf( 'Invalid class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if ( class_exists( $classname ) === false ) {
				throw new MShop_Coupon_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$provider = new $classname( $this->_getContext(), $item, $code, $provider, $outer );

			if ( ( $provider instanceof $iface ) === false ) {
				$msg = sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $iface );
				throw new MShop_Coupon_Exception( $msg );
			}
		}
		return $provider;
	}
}