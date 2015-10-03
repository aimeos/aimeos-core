<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MAdmin
 * @subpackage Common
 */


/**
 * Common methods for all factories.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class MAdmin_Common_Factory_Base
	extends MShop_Common_Factory_Base
{
	/**
	 * Adds the decorators to the manager object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param MShop_Common_Manager_Interface $manager Manager object
	 * @param string $domain Domain name in lower case, e.g. "product"
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected static function addManagerDecorators( MShop_Context_Item_Interface $context,
		MShop_Common_Manager_Interface $manager, $domain )
	{
		$config = $context->getConfig();

		/** madmin/common/manager/decorators/default
		 * Configures the list of decorators applied to all admin managers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to configure a list of decorator names that should
		 * be wrapped around the original instances of all created managers:
		 *
		 *  madmin/common/manager/decorators/default = array( 'decorator1', 'decorator2' )
		 *
		 * This would wrap the decorators named "decorator1" and "decorator2" around
		 * all controller instances in that order. The decorator classes would be
		 * "MShop_Common_Manager_Decorator_Decorator1" and
		 * "MShop_Common_Manager_Decorator_Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 */
		$decorators = $config->get( 'madmin/common/manager/decorators/default', array() );
		$excludes = $config->get( 'madmin/' . $domain . '/manager/decorators/excludes', array() );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = 'MShop_Common_Manager_Decorator_';
		$manager = self::addDecorators( $context, $manager, $decorators, $classprefix );

		$classprefix = 'MShop_Common_Manager_Decorator_';
		$decorators = $config->get( 'madmin/' . $domain . '/manager/decorators/global', array() );
		$manager = self::addDecorators( $context, $manager, $decorators, $classprefix );

		$classprefix = 'MShop_' . ucfirst( $domain ) . '_Manager_Decorator_';
		$decorators = $config->get( 'madmin/' . $domain . '/manager/decorators/local', array() );
		$manager = self::addDecorators( $context, $manager, $decorators, $classprefix );

		return $manager;
	}
}
