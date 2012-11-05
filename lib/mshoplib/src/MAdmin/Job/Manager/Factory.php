<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MAdmin
 * @subpackage Job
 * @version $Id: Factory.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Admin job factory.
 *
 * @package MAdmin
 * @subpackage Job
 */
class MAdmin_Job_Manager_Factory
	extends MShop_Common_Factory_Abstract
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

		if( ctype_alnum( $name ) === false ) {
			throw new MAdmin_Job_Exception( sprintf( 'Invalid class name "%1$s"', $name ) );
		}

		$iface = 'MAdmin_Job_Manager_Interface';
		$classname = 'MAdmin_Job_Manager_' . $name;
		return self::_createManager( $context, $classname, $iface );
	}
}
