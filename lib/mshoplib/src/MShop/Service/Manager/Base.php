<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


/**
 * Abstract class for service managers.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class MShop_Service_Manager_Base
	extends MShop_Common_Manager_ListRef_Base
{
	protected function addServiceDecorators( MShop_Service_Item_Iface $serviceItem,
		MShop_Service_Provider_Iface $provider, $names )
	{
		$iface = 'MShop_Service_Provider_Decorator_Iface';
		$classprefix = 'MShop_Service_Provider_Decorator_';

		foreach( $names as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new MShop_Service_Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new MShop_Service_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$provider = new $classname( $this->getContext(), $serviceItem, $provider );

			if( ( $provider instanceof $iface ) === false ) {
				$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface );
				throw new MShop_Service_Exception( $msg );
			}
		}

		return $provider;
	}
}