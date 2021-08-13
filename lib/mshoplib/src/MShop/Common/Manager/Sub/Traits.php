<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
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
	private $subManagers = [];


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	abstract protected function getContext() : \Aimeos\MShop\Context\Item\Iface;


	/**
	 * Adds the decorators to the manager object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param string[] $decorators List of decorator names that should be wrapped around the manager object
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\MShop\Product\Manager\Decorator\"
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function addDecorators( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Common\Manager\Iface $manager,
		array $decorators, string $classprefix ) : \Aimeos\MShop\Common\Manager\Iface
	{
		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Invalid characters in class name "%1$s"' );
				throw new \Aimeos\MShop\Exception( sprintf( $msg, $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Class "%1$s" not available' );
				throw new \Aimeos\MShop\Exception( sprintf( $msg, $classname ) );
			}

			$manager = new $classname( $manager, $context );

			\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Common\Manager\Decorator\Iface::class, $manager );
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
		$config = $this->getContext()->getConfig();

		$decorators = $config->get( 'mshop/common/manager/decorators/default', [] );
		$excludes = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/excludes', [] );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\Aimeos\MShop\Common\Manager\Decorator\\';
		$manager = $this->addDecorators( $this->getContext(), $manager, $decorators, $classprefix );

		$classprefix = '\Aimeos\MShop\Common\Manager\Decorator\\';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/global', [] );
		$manager = $this->addDecorators( $this->getContext(), $manager, $decorators, $classprefix );

		$subpath = $this->createSubNames( $managerpath );
		$classprefix = '\Aimeos\MShop\\' . ucfirst( $domain ) . '\Manager\\' . $subpath . '\Decorator\\';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/local', [] );

		return $this->addDecorators( $this->getContext(), $manager, $decorators, $classprefix );
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
			if( empty( $subname ) || ctype_alnum( $subname ) === false )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Invalid characters in manager name "%1$s"' );
				throw new \Aimeos\MShop\Exception( sprintf( $msg, $manager ) );
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
	 */
	protected function getSubManagerBase( string $domain, string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$domain = strtolower( $domain );
		$manager = strtolower( $manager );
		$key = $domain . $manager . $name;

		if( !isset( $this->subManagers[$key] ) )
		{
			if( empty( $domain ) || ctype_alnum( $domain ) === false )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Invalid characters in domain name "%1$s"' );
				throw new \Aimeos\MShop\Exception( sprintf( $msg, $domain ) );
			}

			if( preg_match( '/^[a-z0-9\/]+$/', $manager ) !== 1 )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Invalid characters in manager name "%1$s"' );
				throw new \Aimeos\MShop\Exception( sprintf( $msg, $manager ) );
			}

			if( $name === null ) {
				$path = 'mshop/' . $domain . '/manager/' . $manager . '/name';
				$name = $this->getContext()->getConfig()->get( $path, 'Standard' );
			}

			if( empty( $name ) || ctype_alnum( $name ) === false )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Invalid characters in manager name "%1$s"' );
				throw new \Aimeos\MShop\Exception( sprintf( $msg, $name ) );
			}

			$domainname = ucfirst( $domain );
			$subnames = $this->createSubNames( $manager );

			$classname = '\Aimeos\MShop\\' . $domainname . '\Manager\\' . $subnames . '\\' . $name;
			$interface = '\Aimeos\MShop\\' . $domainname . '\Manager\\' . $subnames . '\Iface';

			if( class_exists( $classname ) === false )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Class "%1$s" not available' );
				throw new \Aimeos\MShop\Exception( sprintf( $msg, $classname ) );
			}

			$subManager = \Aimeos\MW\Common\Base::checkClass( $interface, new $classname( $this->getContext() ) );
			$subManager = $this->addManagerDecorators( $subManager, $manager, $domain );
			$this->subManagers[$key] = $subManager->setObject( $subManager );
		}

		return $this->subManagers[$key];
	}
}
