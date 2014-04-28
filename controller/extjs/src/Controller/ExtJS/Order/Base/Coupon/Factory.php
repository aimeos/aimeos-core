<?php

/**
 * @copyright Copyright (c) 2010, Metaways Infosystems GmbH
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS Order base coupon controller factory.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Order_Base_Coupon_Factory implements Controller_ExtJS_Common_Factory_Interface
{
	public static function createController( MShop_Context_Item_Interface $context, $name = null )
	{
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/controller/extjs/order/base/coupon/name', 'Default' );
		}

		$interface = 'Controller_ExtJS_Interface';
		$classname = 'Controller_ExtJS_Order_Base_Coupon_' . $name;
		$filename = 'Controller/ExtJS/Order/Base/Coupon/' . $name . '.php';

		if( ctype_alnum( $name ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
		}

		if( class_exists( $classname ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
		}

		$controller = new $classname( $context );

		if( !( $controller instanceof $interface ) ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" doesn\'t implement "%2$s"', $classname, $interface ) );
		}

		return $controller;
	}
}
