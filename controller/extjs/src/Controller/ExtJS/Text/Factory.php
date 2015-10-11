<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Text;


/**
 * ExtJS text controller factory.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Factory
	extends \Aimeos\Controller\ExtJS\Common\Factory\Base
	implements \Aimeos\Controller\ExtJS\Common\Factory\Iface
{
	public static function createController( \Aimeos\MShop\Context\Item\Iface $context, $name = null )
	{
		/** controller/extjs/text/name
		 * Class name of the used ExtJS text controller implementation
		 *
		 * Each default ExtJS controller can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the client factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\Controller\ExtJS\Text\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\Controller\ExtJS\Text\Mytext
		 *
		 * then you have to set the this configuration option:
		 *
		 *  controller/extjs/text/name = Mytext
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyText"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'controller/extjs/text/name', 'Standard' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\\Aimeos\\Controller\\ExtJS\\Text\\' . $name : '<not a string>';
			throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
		}

		$iface = '\\Aimeos\\Controller\\ExtJS\\Common\\Iface';
		$classname = '\\Aimeos\\Controller\\ExtJS\\Text\\' . $name;

		$controller = self::createControllerBase( $context, $classname, $iface );

		/** controller/extjs/text/decorators/excludes
		 * Excludes decorators added by the "common" option from the text ExtJS controllers
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
		 *  controller/extjs/text/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Controller\ExtJS\Common\Decorator\*") added via
		 * "controller/extjs/common/decorators/default" for the text ExtJS controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/extjs/common/decorators/default
		 * @see controller/extjs/text/decorators/global
		 * @see controller/extjs/text/decorators/local
		 */

		/** controller/extjs/text/decorators/global
		 * Adds a list of globally available decorators only to the text ExtJS controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Controller\ExtJS\Common\Decorator\*") around the ExtJS controller.
		 *
		 *  controller/extjs/text/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Controller\ExtJS\Common\Decorator\Decorator1" only to the ExtJS controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/extjs/common/decorators/default
		 * @see controller/extjs/text/decorators/excludes
		 * @see controller/extjs/text/decorators/local
		 */

		/** controller/extjs/text/decorators/local
		 * Adds a list of local decorators only to the text ExtJS controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Controller\ExtJS\Text\Decorator\*") around the ExtJS controller.
		 *
		 *  controller/extjs/text/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Controller\ExtJS\Text\Decorator\Decorator2" only to the ExtJS
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/extjs/common/decorators/default
		 * @see controller/extjs/text/decorators/excludes
		 * @see controller/extjs/text/decorators/global
		 */
		return self::addControllerDecorators( $context, $controller, 'text' );
	}
}
