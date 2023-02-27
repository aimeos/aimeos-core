<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Sub;


/**
 * Common trait for creating sub-managers
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	private array $subManagers = [];


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	abstract protected function context() : \Aimeos\MShop\ContextIface;


	/**
	 * Adds the decorators to the manager object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context instance with necessary objects
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param string[] $decorators List of decorator names that should be wrapped around the manager object
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\MShop\Product\Manager\Decorator\"
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function addDecorators( \Aimeos\MShop\ContextIface $context, \Aimeos\MShop\Common\Manager\Iface $manager,
		array $decorators, string $classprefix ) : \Aimeos\MShop\Common\Manager\Iface
	{
		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new \LogicException( sprintf( 'Invalid characters in class name "%1$s"', $name ), 400 );
			}

			$classname = $classprefix . $name;
			$interface = \Aimeos\MShop\Common\Manager\Decorator\Iface::class;

			$manager = \Aimeos\Utils::create( $classname, [$manager, $context], $interface );
		}

		return $manager;
	}


	/**
	 * Adds the configured decorators to the given manager object.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param string $managerpath Manager sub-names separated by slashes, e.g. "lists/type"
	 * @param string $domain Domain name in lower case, e.g. "product"
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function addManagerDecorators( \Aimeos\MShop\Common\Manager\Iface $manager, string $managerpath,
		string $domain ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$config = $this->context()->config();

		$decorators = $config->get( 'mshop/common/manager/decorators/default', [] );
		$excludes = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/excludes', [] );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\Aimeos\MShop\Common\Manager\Decorator\\';
		$manager = $this->addDecorators( $this->context(), $manager, $decorators, $classprefix );

		$classprefix = '\Aimeos\MShop\Common\Manager\Decorator\\';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/global', [] );
		$manager = $this->addDecorators( $this->context(), $manager, $decorators, $classprefix );

		$subpath = $this->createSubNames( $managerpath );
		$classprefix = '\Aimeos\MShop\\' . ucfirst( $domain ) . '\Manager\\' . $subpath . '\Decorator\\';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/local', [] );

		return $this->addDecorators( $this->context(), $manager, $decorators, $classprefix );
	}


	/**
	 * Transforms the manager path to the appropriate class names.
	 *
	 * @param string $manager Path of manager names, e.g. "lists/type"
	 * @return string Class names, e.g. "List_Type"
	 */
	protected function createSubNames( string $manager ) : string
	{
		$names = explode( '/', $manager );

		foreach( $names as $key => $subname )
		{
			if( empty( $subname ) || ctype_alnum( $subname ) === false ) {
				throw new \LogicException( sprintf( 'Invalid characters in manager name "%1$s"', $manager ), 400 );
			}

			$names[$key] = ucfirst( $subname );
		}

		return implode( '\\', $names );
	}


	/**
	 * Returns a new manager the given extension name.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string $manager Name of the sub manager type in lower case (can contain a path like base/product)
	 * @param string|null $name Name of the implementation, will be from configuration (or Standard) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions
	 * @throws \LogicException If class isn't found
	 */
	protected function getSubManagerBase( string $domain, string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$context = $this->context();
		$domain = strtolower( $domain );
		$manager = strtolower( $manager );

		$name = $name ?: $context->config()->get( 'mshop/' . $domain . '/manager/' . $manager . '/name', 'Standard' );
		$key = $domain . $manager . $name;

		if( !isset( $this->subManagers[$key] ) )
		{
			if( empty( $domain ) || ctype_alnum( $domain ) === false ) {
				throw new \LogicException( sprintf( 'Invalid characters in domain name "%1$s"', $domain ), 400 );
			}

			if( preg_match( '/^[a-z0-9\/]+$/', $manager ) !== 1 ) {
				throw new \LogicException( sprintf( 'Invalid characters in manager name "%1$s"', $manager ), 400 );
			}

			if( empty( $name ) || ctype_alnum( $name ) === false ) {
				throw new \LogicException( sprintf( 'Invalid characters in manager name "%1$s"', $name ), 400 );
			}

			$domainname = ucfirst( $domain );
			$subnames = $this->createSubNames( $manager );

			$classname = '\Aimeos\MShop\\' . $domainname . '\Manager\\' . $subnames . '\\' . $name;
			$interface = '\Aimeos\MShop\\' . $domainname . '\Manager\\' . $subnames . '\Iface';

			$subManager = \Aimeos\Utils::create( $classname, [$context], $interface );

			$subManager = $this->addManagerDecorators( $subManager, $manager, $domain );
			$this->subManagers[$key] = $subManager->setObject( $subManager );
		}

		return $this->subManagers[$key];
	}
}
