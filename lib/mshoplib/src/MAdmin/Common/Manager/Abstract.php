<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MAdmin
 * @subpackage Common
 */


/**
 * Provides common methods required by most of the manager classes.
 *
 * @package MAdmin
 * @subpackage Common
 */
abstract class MAdmin_Common_Manager_Abstract extends MShop_Common_Manager_Abstract
{
	/**
	 * Adds the configured decorators to the given manager object.
	 *
	 * @param MShop_Common_Manager_Interface $manager Manager object
	 * @param string $managerpath Manager sub-names separated by slashes, e.g. "list/type"
	 * @param string $domain Domain name in lower case, e.g. "product"
	 */
	protected function _addManagerDecorators( MShop_Common_Manager_Interface $manager, $managerpath, $domain )
	{
		$context = $this->_getContext();
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
		$excludes = $config->get( 'madmin/' . $domain . '/manager/' . $managerpath . '/decorators/excludes', array() );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = 'MShop_Common_Manager_Decorator_';
		$manager = $this->_addDecorators( $context, $manager, $decorators, $classprefix );

		$classprefix = 'MShop_Common_Manager_Decorator_';
		$decorators = $config->get( 'madmin/' . $domain . '/manager/' . $managerpath . '/decorators/global', array() );
		$manager = $this->_addDecorators( $context, $manager, $decorators, $classprefix );

		$subpath = $this->_createSubNames( $managerpath );
		$classprefix = 'MShop_' . ucfirst( $domain ) . '_Manager_' . $subpath . '_Decorator_';
		$decorators = $config->get( 'madmin/' . $domain . '/manager/' . $managerpath . '/decorators/local', array() );

		return $this->_addDecorators( $context, $manager, $decorators, $classprefix );
	}


	/**
	 * Returns a new manager the given extension name
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string $manager Name of the sub manager type in lower case (can contain a path like base/product)
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Interface Manager for different extensions
	 */
	protected function _getSubManager( $domain, $manager, $name )
	{
		$domain = strtolower( $domain );
		$manager = strtolower( $manager );
		$config = $this->_getContext()->getConfig();


		if( empty( $domain ) || ctype_alnum( $domain ) === false ) {
			throw new MAdmin_Exception( sprintf( 'Invalid characters in domain name "%1$s"', $domain ) );
		}

		if( $name === null ) {
			$name = $config->get( 'classes/' . $domain . '/manager/' . $manager . '/name', 'Default' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new MAdmin_Exception( sprintf( 'Invalid characters in manager name "%1$s"', $name ) );
		}

		$domainname = ucfirst( $domain );
		$subnames = $this->_createSubNames( $manager );

		$classname = 'MAdmin_' . $domainname . '_Manager_' . $subnames . '_' . $name;
		$interface = 'MAdmin_' . $domainname . '_Manager_' . $subnames . '_Interface';

		if( class_exists( $classname ) === false ) {
			throw new MAdmin_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$subManager = new $classname( $this->_getContext() );

		if( ( $subManager instanceof $interface ) === false ) {
			throw new MAdmin_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $subManager;
	}
}
