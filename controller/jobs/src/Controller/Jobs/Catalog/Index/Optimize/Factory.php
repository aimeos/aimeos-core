<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Catalog index controller factory.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Catalog_Index_Optimize_Factory
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
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/controller/jobs/catalog/index/optimize/name', 'Default');
		}

		if ( ctype_alnum($name) === false )
		{
			$classname = is_string($name) ? 'Controller_Jobs_Catalog_Index_Optimize_' . $name : '<not a string>';
			throw new Controller_Jobs_Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_Jobs_Interface';
		$classname = 'Controller_Jobs_Catalog_Index_Optimize_' . $name;

		$controller = self::_createController( $context, $arcavias, $classname, $iface );
		return self::_addControllerDecorators( $context, $arcavias, $controller, 'catalog/index/optimize' );
	}
}