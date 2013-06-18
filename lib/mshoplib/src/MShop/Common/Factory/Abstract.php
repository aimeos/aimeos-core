<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Common methods for all factories.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class MShop_Common_Factory_Abstract
{
	/**
	 * Adds the decorators to the manager object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param MShop_Common_Manager_Interface $manager Manager object
	 * @param string $classprefix Decorator class prefix, e.g. "MShop_Product_Manager_Decorator_"
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected static function _addDecorators( MShop_Context_Item_Interface $context,
		MShop_Common_Manager_Interface $manager, array $decorators, $classprefix )
	{
		$iface = 'MShop_Common_Manager_Decorator_Interface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new MShop_Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new MShop_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$manager =  new $classname( $context, $manager );

			if( !( $manager instanceof $iface ) ) {
				throw new MShop_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
			}
		}

		return $manager;
	}


	/**
	 * Adds the decorators to the manager object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param MShop_Common_Manager_Interface $manager Manager object
	 * @param string $domain Domain name in lower case, e.g. "product"
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected static function _addManagerDecorators( MShop_Context_Item_Interface $context,
		MShop_Common_Manager_Interface $manager, $domain )
	{
		$config = $context->getConfig();

		$decorators = $config->get( 'mshop/common/manager/decorators/default', array() );
		$excludes = $config->get( 'mshop/' . $domain . '/manager/decorators/excludes', array() );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[ $key ] );
			}
		}

		$classprefix = 'MShop_Common_Manager_Decorator_';
		$manager =  self::_addDecorators( $context, $manager, $decorators, $classprefix );

		$classprefix = 'MShop_Common_Manager_Decorator_';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/decorators/global', array() );
		$manager =  self::_addDecorators( $context, $manager, $decorators, $classprefix );

		$classprefix = 'MShop_'. ucfirst( $domain ) . '_Manager_Decorator_';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/decorators/local', array() );
		$manager =  self::_addDecorators( $context, $manager, $decorators, $classprefix );

		return $manager;
	}


	/**
	 * Creates a manager object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param string $classname Name of the manager class
	 * @param string $interface Name of the manager interface
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected static function _createManager( MShop_Context_Item_Interface $context, $classname, $interface )
	{
		if( class_exists( $classname ) === false ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$manager =  new $classname( $context );

		if( !( $manager instanceof $interface ) ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $manager;
	}
}
