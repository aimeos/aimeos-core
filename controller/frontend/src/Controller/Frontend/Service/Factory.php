<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Service frontend controller factory.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Controller_Frontend_Service_Factory
	extends Controller_Frontend_Common_Factory_Abstract
	implements Controller_Frontend_Common_Factory_Interface
{
	public static function createController( MShop_Context_Item_Interface $context, $name = null )
	{
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/controller/frontend/service/name', 'Default');
		}

		if ( ctype_alnum($name) === false ) {
			$classname = is_string($name) ? 'Controller_Frontend_Service_' . $name : '<not a string>';
			throw new Controller_Frontend_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_Frontend_Service_Interface';
		$classname = 'Controller_Frontend_Service_' . $name;

		$manager = self::_createController( $context, $classname, $iface );
		return self::_addControllerDecorators( $context, $manager, 'service' );
	}
}
