<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * Attribute list type controller factory.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Attribute_List_Type_Factory
	extends Controller_ExtJS_Common_Factory_Base
	implements Controller_ExtJS_Common_Factory_Iface
{
	public static function createController( MShop_Context_Item_Iface $context, $name = null )
	{
		/** classes/controller/extjs/attribute/list/type/name
		 * Class name of the used ExtJS attribute list type controller implementation
		 *
		 * Each default ExtJS controller can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the client factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  Controller_ExtJS_Attribute_List_Type_Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  Controller_ExtJS_Attribute_List_Type_Mytype
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/controller/extjs/attribute/list/type/name = Mytype
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyType"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/controller/extjs/attribute/list/type/name', 'Standard' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'Controller_ExtJS_Attribute_List_Type_' . $name : '<not a string>';
			throw new Controller_ExtJS_Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_ExtJS_Common_Iface';
		$classname = 'Controller_ExtJS_Attribute_List_Type_' . $name;

		$controller = self::createControllerBase( $context, $classname, $iface );

		/** controller/extjs/attribute/list/type/decorators/excludes
		 * Excludes decorators added by the "common" option from the attribute list type ExtJS controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "controller/extjs/common/decorators/default" before they are wrapped
		 * around the ExtJS controller.
		 *
		 *  controller/extjs/attribute/list/type/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("Controller_ExtJS_Common_Decorator_*") added via
		 * "controller/extjs/common/decorators/default" for the admin ExtJS controller.
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/extjs/common/decorators/default
		 * @see controller/extjs/attribute/list/type/decorators/global
		 * @see controller/extjs/attribute/list/type/decorators/local
		 */

		/** controller/extjs/attribute/list/type/decorators/global
		 * Adds a list of globally available decorators only to the attribute list type ExtJS controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("Controller_ExtJS_Common_Decorator_*") around the ExtJS controller.
		 *
		 *  controller/extjs/attribute/list/type/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "Controller_ExtJS_Common_Decorator_Decorator1" only to the ExtJS controller.
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/extjs/common/decorators/default
		 * @see controller/extjs/attribute/list/type/decorators/excludes
		 * @see controller/extjs/attribute/list/type/decorators/local
		 */

		/** controller/extjs/attribute/list/type/decorators/local
		 * Adds a list of local decorators only to the attribute list type ExtJS controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("Controller_ExtJS_Attribute_List_Type_Decorator_*") around the ExtJS controller.
		 *
		 *  controller/extjs/attribute/list/type/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "Controller_ExtJS_Attribute_List_Type_Decorator_Decorator2" only to the ExtJS
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/extjs/common/decorators/default
		 * @see controller/extjs/attribute/list/type/decorators/excludes
		 * @see controller/extjs/attribute/list/type/decorators/global
		 */
		return self::addControllerDecorators( $context, $controller, 'attribute/list/type' );
	}
}
