<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Order coupon count controller factory.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Order_Coupon_Count_Factory
	extends Controller_Jobs_Common_Factory_Abstract
	implements Controller_Jobs_Common_Factory_Interface
{
	/**
	 * Creates a new controller specified by the given name.
	 *
	 * @param MShop_Context_Item_Interface $context Context object required by controllers
	 * @param Arcavias $arcavias Arcavias object
	 * @param string|null $name Name of the controller or "Default" if null
	 * @return Controller_Jobs_Interface New controller object
	 */
	public static function createController( MShop_Context_Item_Interface $context, Arcavias $arcavias, $name = null )
	{
		/** classes/controller/jobs/order/coupon/count/name
		 * Class name of the used order coupon count scheduler controller implementation
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 * @deprecated Use classes/controller/jobs/order/cleanup/resource/name instead
		 */
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/controller/jobs/order/coupon/count/name', 'Default');
		}

		if ( ctype_alnum($name) === false )
		{
			$classname = is_string($name) ? 'Controller_Jobs_Order_Coupon_Count_' . $name : '<not a string>';
			throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_Jobs_Interface';
		$classname = 'Controller_Jobs_Order_Coupon_Count_' . $name;

		$controller = self::_createController( $context, $arcavias, $classname, $iface );
		return self::_addControllerDecorators( $context, $arcavias, $controller, 'order/coupon/count' );
	}
}