<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Frontend service test factory.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Controller_Frontend_Service_Factorylocal
	extends Controller_Frontend_Common_Factory_Abstract
{
	/**
	 * @param string $name
	 */
	public static function createController( MShop_Context_Item_Interface $context, $name = null, $domainToTest='service' )
	{
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/controller/frontend/service/name', 'Default');
		}

		if ( ctype_alnum($name) === false ) {
			throw new Controller_Frontend_Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
		}

		$iface = 'Controller_Frontend_Service_Interface';
		$classname = 'Controller_Frontend_Service_' . $name;

		$manager = self::_createController( $context, $classname, $iface );
		return self::_addControllerDecorators( $context, $manager, $domainToTest );
	}
}
