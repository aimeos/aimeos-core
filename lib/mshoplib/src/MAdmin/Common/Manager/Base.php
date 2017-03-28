<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MAdmin
 * @subpackage Common
 */


namespace Aimeos\MAdmin\Common\Manager;


/**
 * Provides common methods required by most of the manager classes.
 *
 * @package MAdmin
 * @subpackage Common
 */
abstract class Base extends \Aimeos\MShop\Common\Manager\Base
{
	/**
	 * Adds the configured decorators to the given manager object.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param string $managerpath Manager sub-names separated by slashes, e.g. "list/type"
	 * @param string $domain Domain name in lower case, e.g. "product"
	 */
	protected function addManagerDecorators( \Aimeos\MShop\Common\Manager\Iface $manager, $managerpath, $domain )
	{
		$context = $this->getContext();
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
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" and
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 */
		$decorators = $config->get( 'madmin/common/manager/decorators/default', [] );
		$excludes = $config->get( 'madmin/' . $domain . '/manager/' . $managerpath . '/decorators/excludes', [] );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\\Aimeos\\MShop\\Common\\Manager\\Decorator\\';
		$manager = $this->addDecorators( $context, $manager, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\MShop\\Common\\Manager\\Decorator\\';
		$decorators = $config->get( 'madmin/' . $domain . '/manager/' . $managerpath . '/decorators/global', [] );
		$manager = $this->addDecorators( $context, $manager, $decorators, $classprefix );

		$subpath = $this->createSubNames( $managerpath );
		$classprefix = 'MShop_' . ucfirst( $domain ) . '_Manager_' . $subpath . '_Decorator_';
		$decorators = $config->get( 'madmin/' . $domain . '/manager/' . $managerpath . '/decorators/local', [] );

		return $this->addDecorators( $context, $manager, $decorators, $classprefix );
	}


	/**
	 * Returns a new manager the given extension name
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string $manager Name of the sub manager type in lower case (can contain a path like base/product)
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions
	 */
	protected function getSubManagerBase( $domain, $manager, $name )
	{
		$domain = strtolower( $domain );
		$manager = strtolower( $manager );
		$config = $this->getContext()->getConfig();


		if( empty( $domain ) || ctype_alnum( $domain ) === false ) {
			throw new \Aimeos\MAdmin\Exception( sprintf( 'Invalid characters in domain name "%1$s"', $domain ) );
		}

		if( $name === null ) {
			$name = $config->get( 'mshop/' . $domain . '/manager/' . $manager . '/name', 'Standard' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new \Aimeos\MAdmin\Exception( sprintf( 'Invalid characters in manager name "%1$s"', $name ) );
		}

		$domainname = ucfirst( $domain );
		$subnames = $this->createSubNames( $manager );

		$classname = '\\Aimeos\\MAdmin\\' . $domainname . '\\Manager\\' . $subnames . '\\' . $name;
		$interface = '\\Aimeos\\MAdmin\\' . $domainname . '\\Manager\\' . $subnames . '\\Iface';

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MAdmin\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$subManager = new $classname( $this->getContext() );

		if( ( $subManager instanceof $interface ) === false ) {
			throw new \Aimeos\MAdmin\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $subManager;
	}
}
