<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Text
 */


namespace Aimeos\MShop\Text\Manager;


/**
 * Factory for the text domain manager
 *
 * @package MShop
 * @subpackage Text
 */
class Factory
	extends \Aimeos\MShop\Common\Factory\Base
	implements \Aimeos\MShop\Common\Factory\Iface
{
	/**
	 *	Creates a text manager DAO object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Shop context instance with necessary objects
	 * @param string|null $name Manager name
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object implementing the manager interface
	 * @throws \Aimeos\MShop\Text\Exception|\Aimeos\MShop\Exception If requested manager
	 * implementation couldn't be found or initialisation fails
	 */
	public static function createManager( \Aimeos\MShop\Context\Item\Iface $context, $name = null )
	{
		/** mshop/text/manager/name
		 * Class name of the used text manager implementation
		 *
		 * Each default manager can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Text\Manager\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Text\Manager\Mymanager
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/text/manager/name = Mymanager
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyManager"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'mshop/text/manager/name', 'Standard' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\\Aimeos\\MShop\\Text\\Manager\\' . $name : '<not a string>';
			throw new \Aimeos\MShop\Text\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = '\\Aimeos\\MShop\\Text\\Manager\\Iface';
		$classname = '\\Aimeos\\MShop\\Text\\Manager\\' . $name;

		$manager = self::createManagerBase( $context, $classname, $iface );

		/** mshop/text/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the text manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the text manager.
		 *
		 *  mshop/text/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the text manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/text/manager/decorators/global
		 * @see mshop/text/manager/decorators/local
		 */

		/** mshop/text/manager/decorators/global
		 * Adds a list of globally available decorators only to the text manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the text manager.
		 *
		 *  mshop/text/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the text controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/text/manager/decorators/excludes
		 * @see mshop/text/manager/decorators/local
		 */

		/** mshop/text/manager/decorators/local
		 * Adds a list of local decorators only to the text manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the text manager.
		 *
		 *  mshop/text/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the text
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/text/manager/decorators/excludes
		 * @see mshop/text/manager/decorators/global
		 */
		return self::addManagerDecorators( $context, $manager, 'text' );
	}
}