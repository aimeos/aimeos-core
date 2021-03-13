<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Manager;


/**
 * Abstract class for rule managers.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
{
	/**
	 * Returns the rule provider which is responsible for the rule item.
	 *
	 * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
	 * @param string $type Rule type code
	 * @return \Aimeos\MShop\Rule\Provider\Iface Returns the decoratad rule provider object
	 * @throws \Aimeos\MShop\Rule\Exception If provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Rule\Item\Iface $item, string $type ) : \Aimeos\MShop\Rule\Provider\Iface
	{
		$type = ucwords( $type );
		$names = explode( ',', $item->getProvider() );

		if( ctype_alnum( $type ) === false ) {
			throw new \Aimeos\MShop\Rule\Exception( sprintf( 'Invalid characters in type name "%1$s"', $type ) );
		}

		if( ( $provider = array_shift( $names ) ) === null ) {
			throw new \Aimeos\MShop\Rule\Exception( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ) );
		}

		if( ctype_alnum( $provider ) === false ) {
			throw new \Aimeos\MShop\Rule\Exception( sprintf( 'Invalid characters in provider name "%1$s"', $provider ) );
		}

		$classname = '\Aimeos\MShop\Rule\Provider\\' . $type . '\\' . $provider;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MShop\Rule\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$context = $this->getContext();
		$config = $context->getConfig();
		$provider = new $classname( $context, $item );

		self::checkClass( \Aimeos\MShop\Rule\Provider\Factory\Iface::class, $provider );
		$provider = $this->addRuleDecorators( $item, $provider, $names, $type );

		return $provider->setObject( $provider );
	}


	/**
	 *
	 * @param \Aimeos\MShop\Rule\Item\Iface $ruleItem Rule item object
	 * @param \Aimeos\MShop\Rule\Provider\Iface $provider Rule provider object
	 * @param array $names List of decorator names that should be wrapped around the rule provider object
	 * @param string $type Rule type code
	 * @return \Aimeos\MShop\Rule\Provider\Iface Rule provider object
	 */
	protected function addRuleDecorators( \Aimeos\MShop\Rule\Item\Iface $ruleItem,
		\Aimeos\MShop\Rule\Provider\Iface $provider, array $names, string $type ) : \Aimeos\MShop\Rule\Provider\Iface
	{
		$classprefix = '\Aimeos\MShop\Rule\Provider\\' . $type . '\Decorator\\';

		foreach( $names as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new \Aimeos\MShop\Rule\Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\MShop\Rule\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$provider = new $classname( $this->getContext(), $ruleItem, $provider );

			self::checkClass( $classprefix . 'Iface', $provider );
		}

		return $provider;
	}
}
