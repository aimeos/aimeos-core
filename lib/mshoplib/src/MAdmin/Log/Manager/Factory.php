<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MAdmin
 * @subpackage Log
 * @version $Id: Factory.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Admin log factory.
 *
 * @package MAdmin
 * @subpackage Log
 */
class MAdmin_Log_Manager_Factory
	extends MShop_Common_Factory_Abstract
	implements MShop_Common_Factory_Interface
{
	/**
	 * Creates an admin log manager object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param string $name Manager name
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	public static function createManager( MShop_Context_Item_Interface $context, $name = null )
	{
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/log/manager/name', 'Default' );
		}

		if( ctype_alnum( $name ) === false ) {
			throw new MAdmin_Log_Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
		}

		$iface = 'MAdmin_Log_Manager_Interface';
		$classname = 'MAdmin_Log_Manager_' . $name;
		return self::_createManager( $context, $classname, $iface );
	}
}
