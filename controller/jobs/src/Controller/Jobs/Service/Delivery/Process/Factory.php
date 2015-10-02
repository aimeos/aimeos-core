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
 * @deprecated Use Controller_Jobs_Order_Service_Delivery_Factory instead
 */
class Controller_Jobs_Service_Delivery_Process_Factory
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
		/** classes/controller/jobs/service/delivery/process/name
		 * Class name of the used service delivery process scheduler controller implementation
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 * @deprecated Use classes/controller/jobs/order/service/delivery/name instead
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/controller/jobs/service/delivery/process/name', 'Default' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'Controller_Jobs_Service_Delivery_Process_' . $name : '<not a string>';
			throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_Jobs_Interface';
		$classname = 'Controller_Jobs_Service_Delivery_Process_' . $name;

		$controller = self::createControllerBase( $context, $aimeos, $classname, $iface );

		/** controller/jobs/service/delivery/process/decorators/excludes
		 * Excludes decorators added by the "common" option from the service job controllers
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/service/delivery/process/decorators/global
		 * @see controller/jobs/service/delivery/process/decorators/local
		 * @deprecated Use controller/jobs/order/service/delivery/decorators/excludes instead
		 */

		/** controller/jobs/service/delivery/process/decorators/global
		 * Adds a list of globally available decorators only to the service job controllers
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/service/delivery/process/decorators/excludes
		 * @see controller/jobs/service/delivery/process/decorators/local
		 * @deprecated Use controller/jobs/order/service/delivery/decorators/global instead
		 */

		/** controller/jobs/service/delivery/process/decorators/local
		 * Adds a list of local decorators only to the service jos controllers
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/service/delivery/process/decorators/excludes
		 * @see controller/jobs/service/delivery/process/decorators/global
		 * @deprecated Use controller/jobs/order/service/delivery/decorators/local instead
		 */
		return self::_addControllerDecorators( $context, $aimeos, $controller, 'service/delivery/process' );
	}
}