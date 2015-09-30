<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
	 * @param Aimeos $aimeos Aimeos object
	 * @param string|null $name Name of the controller or "Default" if null
	 * @return Controller_Jobs_Interface New controller object
	 */
	public static function createController( MShop_Context_Item_Interface $context, Aimeos $aimeos, $name = null )
	{
		/** classes/controller/jobs/order/coupon/count/name
		 * Class name of the used order coupon count scheduler controller implementation
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 * @deprecated Use classes/controller/jobs/order/cleanup/unfinished/name instead
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/controller/jobs/order/coupon/count/name', 'Default' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'Controller_Jobs_Order_Coupon_Count_' . $name : '<not a string>';
			throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_Jobs_Interface';
		$classname = 'Controller_Jobs_Order_Coupon_Count_' . $name;

		$controller = self::_createController( $context, $aimeos, $classname, $iface );

		/** controller/jobs/order/coupon/count/decorators/excludes
		 * Excludes decorators added by the "common" option from the order coupon count controllers
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/order/coupon/count/decorators/global
		 * @see controller/jobs/order/coupon/count/decorators/local
		 * @deprecated Use controller/jobs/order/cleanup/unfinished/decorators/excludes instead
		 */

		/** controller/jobs/order/coupon/count/decorators/global
		 * Adds a list of globally available decorators only to the order coupon count controllers
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/order/coupon/count/decorators/excludes
		 * @see controller/jobs/order/coupon/count/decorators/local
		 * @deprecated Use controller/jobs/order/cleanup/unfinished/decorators/global instead
		 */

		/** controller/jobs/order/coupon/count/decorators/local
		 * Adds a list of local decorators only to the order coupon count controllers
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/order/coupon/count/decorators/excludes
		 * @see controller/jobs/order/coupon/count/decorators/global
		 * @deprecated Use controller/jobs/order/cleanup/unfinished/decorators/local instead
		 */
		return self::_addControllerDecorators( $context, $aimeos, $controller, 'order/coupon/count' );
	}
}