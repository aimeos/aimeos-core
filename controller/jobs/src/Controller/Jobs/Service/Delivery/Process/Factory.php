<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	 * @param Arcavias $arcavias Arcavias object
	 * @param string|null $name Name of the controller or "Default" if null
	 * @return Controller_Jobs_Interface New controller object
	 */
	public static function createController( MShop_Context_Item_Interface $context, Arcavias $arcavias, $name = null )
	{
		/** classes/controller/jobs/service/delivery/process/name
		 * Class name of the used service delivery process scheduler controller implementation
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 * @deprecated Use classes/controller/jobs/order/service/delivery/name instead
		 */
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/controller/jobs/service/delivery/process/name', 'Default');
		}

		if ( ctype_alnum($name) === false )
		{
			$classname = is_string($name) ? 'Controller_Jobs_Service_Delivery_Process_' . $name : '<not a string>';
			throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_Jobs_Interface';
		$classname = 'Controller_Jobs_Service_Delivery_Process_' . $name;

		$controller = self::_createController( $context, $arcavias, $classname, $iface );

		/** controller/jobs/service/decorators/excludes
		 * Excludes decorators added by the "common" option from the service job controllers
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/service/decorators/global
		 * @see controller/jobs/service/decorators/local
		 * @deprecated Use controller/jobs/order/decorators/excludes instead
		 */

		/** controller/jobs/service/decorators/global
		 * Adds a list of globally available decorators only to the service job controllers
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/service/decorators/excludes
		 * @see controller/jobs/service/decorators/local
		 * @deprecated Use controller/jobs/order/decorators/global instead
		 */

		/** controller/jobs/service/decorators/local
		 * Adds a list of local decorators only to the service jos controllers
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/service/decorators/excludes
		 * @see controller/jobs/service/decorators/global
		 * @deprecated Use controller/jobs/order/decorators/local instead
		 */
		return self::_addControllerDecorators( $context, $arcavias, $controller, 'service/delivery/process' );
	}
}