<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Order stock controller factory.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Order_Product_Stock_Factory
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
		/** classes/controller/jobs/order/product/stock/name
		 * Class name of the used order product stock scheduler controller implementation
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 * @deprecated Use classes/controller/jobs/order/cleanup/unfinished/name instead
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/controller/jobs/order/product/stock/name', 'Default' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'Controller_Jobs_Order_Product_Stock_' . $name : '<not a string>';
			throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_Jobs_Interface';
		$classname = 'Controller_Jobs_Order_Product_Stock_' . $name;

		$controller = self::createControllerBase( $context, $aimeos, $classname, $iface );

		/** controller/jobs/order/product/stock/decorators/excludes
		 * Excludes decorators added by the "common" option from the order product stock controllers
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/order/product/stock/decorators/global
		 * @see controller/jobs/order/product/stock/decorators/local
		 * @deprecated Use controller/jobs/order/cleanup/unfinished/decorators/excludes instead
		 */

		/** controller/jobs/order/product/stock/decorators/global
		 * Adds a list of globally available decorators only to the order product stock controllers
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/order/product/stock/decorators/excludes
		 * @see controller/jobs/order/product/stock/decorators/local
		 * @deprecated Use controller/jobs/order/cleanup/unfinished/decorators/global instead
		 */

		/** controller/jobs/order/product/stock/decorators/local
		 * Adds a list of local decorators only to the order product stock controllers
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/order/product/stock/decorators/excludes
		 * @see controller/jobs/order/product/stock/decorators/global
		 * @deprecated Use controller/jobs/order/cleanup/unfinished/decorators/local instead
		 */
		return self::_addControllerDecorators( $context, $aimeos, $controller, 'order/product/stock' );
	}
}