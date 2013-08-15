<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Admin job controller factory.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Admin_Job_Factory
	extends Controller_Jobs_Common_Factory_Abstract
	implements Controller_Jobs_Common_Factory_Interface
{
	public static function createController( MShop_Context_Item_Interface $context, $name = null )
	{
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/controller/jobs/admin/job/name', 'Default');
		}

		if ( ctype_alnum($name) === false )
		{
			$classname = is_string($name) ? 'Controller_Jobs_Admin_Job_' . $name : '<not a string>';
			throw new Controller_Jobs_Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_Jobs_Interface';
		$classname = 'Controller_Jobs_Admin_Job_' . $name;

		$controller = self::_createController( $context, $classname, $iface );
		return self::_addControllerDecorators( $context, $controller, 'admin/job' );
	}
}