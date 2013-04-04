<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 * @version $Id: Abstract.php 14661 2012-01-03 16:32:47Z nsendetzky $
 */


/**
 * Abstract class for plugin managers.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class MShop_Plugin_Manager_Abstract
	extends MShop_Common_Manager_Abstract
{
	protected function _addPluginDecorators( MShop_Plugin_Item_Interface $serviceItem,
		MShop_Plugin_Provider_Interface $provider, $names )
	{
		$iface = 'MShop_Plugin_Provider_Decorator_Interface';
		$classprefix = 'MShop_Plugin_Provider_Decorator_';

		foreach( $names as $name )
		{
			if ( ctype_alnum( $name ) === false ) {
				throw new MShop_Plugin_Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if ( class_exists( $classname ) === false ) {
				throw new MShop_Plugin_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$provider = new $classname( $this->_getContext(), $serviceItem, $provider );

			if ( ( $provider instanceof $iface ) === false ) {
				$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface );
				throw new MShop_Plugin_Exception( $msg );
			}
		}

		return $provider;
	}
}