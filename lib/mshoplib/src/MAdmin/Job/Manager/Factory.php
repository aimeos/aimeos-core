<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MAdmin
 * @subpackage Job
 */


namespace Aimeos\MAdmin\Job\Manager;


/**
 * Admin job factory.
 *
 * @package MAdmin
 * @subpackage Job
 */
class Factory
	extends \Aimeos\MAdmin\Common\Factory\Base
	implements \Aimeos\MShop\Common\Factory\Iface
{
	/**
	 * Creates an admin job manager object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param string|null $name Manager name
	 * @return \Aimeos\MAdmin\Job\Manager\Iface Job manager object
	 */
	public static function createManager( \Aimeos\MShop\Context\Item\Iface $context, $name = null )
	{
		/** madmin/job/manager/name
		 * Class name of the used job manager implementation
		 *
		 * Each default manager can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Job\Manager\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Job\Manager\Mymanager
		 *
		 * then you have to set the this configuration option:
		 *
		 *  madmin/job/manager/name = Mymanager
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
			$name = $context->getConfig()->get( 'madmin/job/manager/name', 'Standard' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\\Aimeos\\MAdmin\\Job\\Manager\\' . $name : '<not a string>';
			throw new \Aimeos\MAdmin\Job\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = '\\Aimeos\\MAdmin\\Job\\Manager\\Iface';
		$classname = '\\Aimeos\\MAdmin\\Job\\Manager\\' . $name;

		$manager = self::createManagerBase( $context, $classname, $iface );

		/** madmin/job/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the job manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "madmin/common/manager/decorators/default" before they are wrapped
		 * around the job manager.
		 *
		 *  madmin/job/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "madmin/common/manager/decorators/default" for the job manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/common/manager/decorators/default
		 * @see madmin/job/manager/decorators/global
		 * @see madmin/job/manager/decorators/local
		 */

		/** madmin/job/manager/decorators/global
		 * Adds a list of globally available decorators only to the job manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the job manager.
		 *
		 *  madmin/job/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the job controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/common/manager/decorators/default
		 * @see madmin/job/manager/decorators/excludes
		 * @see madmin/job/manager/decorators/local
		 */

		/** madmin/job/manager/decorators/local
		 * Adds a list of local decorators only to the job manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the job manager.
		 *
		 *  madmin/job/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the job
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/common/manager/decorators/default
		 * @see madmin/job/manager/decorators/excludes
		 * @see madmin/job/manager/decorators/global
		 */
		return self::addManagerDecorators( $context, $manager, 'job' );
	}
}
