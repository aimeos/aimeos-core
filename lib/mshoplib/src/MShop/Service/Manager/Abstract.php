<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 * @version $Id: Abstract.php 14661 2012-01-03 16:32:47Z nsendetzky $
 */


/**
 * Abstract class for service managers.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class MShop_Service_Manager_Abstract
	extends MShop_Common_Manager_Abstract
{
	protected function _addServiceDecorators( MShop_Service_Item_Interface $serviceItem,
		MShop_Service_Provider_Interface $provider, $names )
	{
		$iface = 'MShop_Service_Provider_Decorator_Interface';
		$classprefix = 'MShop_Service_Provider_Decorator_';

		foreach( $names as $name )
		{
			if ( ctype_alnum( $name ) === false ) {
				throw new MShop_Service_Exception( sprintf( 'Invalid class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if ( class_exists( $classname ) === false ) {
				throw new MShop_Service_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$provider = new $classname( $this->_getContext(), $serviceItem, $provider );

			if ( ( $provider instanceof $iface ) === false ) {
				$msg = sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $iface );
				throw new MShop_Service_Exception( $msg );
			}
		}

		return $provider;
	}
}