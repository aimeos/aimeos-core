<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MAdmin
 * @subpackage Job
 */


/**
 * Admin job factory.
 *
 * @package MAdmin
 * @subpackage Job
 */
class MAdmin_Job_Manager_Factory
	extends MAdmin_Common_Factory_Abstract
	implements MShop_Common_Factory_Interface
{
	/**
	 * Creates an admin job manager object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param string $name Manager name
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	public static function createManager( MShop_Context_Item_Interface $context, $name = null )
	{
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/job/manager/name', 'Default' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'MAdmin_Job_Manager_' . $name : '<not a string>';
			throw new MAdmin_Job_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'MAdmin_Job_Manager_Interface';
		$classname = 'MAdmin_Job_Manager_' . $name;

		$manager = self::_createManager( $context, $classname, $iface );
		return self::_addManagerDecorators( $context, $manager, 'job' );
	}
}
