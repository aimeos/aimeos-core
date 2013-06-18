<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS plugin test factory.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Plugin_Factorylocal
	extends Controller_ExtJS_Common_Factory_Abstract
{
	public static function createController( MShop_Context_Item_Interface $context, $name = null, $domainToTest='plugin' )
	{
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/controller/extjs/plugin/name', 'Default');
		}

		if ( ctype_alnum($name) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Invalid class name "%1$s"', $name ) );
		}

		$iface = 'Controller_ExtJS_Common_Interface';
		$classname = 'Controller_ExtJS_Plugin_' . $name;

		$manager = self::_createController( $context, $classname, $iface );
		return self::_addControllerDecorators( $context, $manager, $domainToTest );
	}
}
