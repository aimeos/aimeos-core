<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
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
	private array $rules = [];


	/**
	 * Applies the rules for modifying items dynamically
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface $items Item or list of items
	 * @param string $type Type of rules to apply to the items (e.g. "basket" or "catalog")
	 * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Modified item or list of items
	 */
	public function apply( $items, string $type = 'catalog' )
	{
		if( !isset( $this->rules[$type] ) )
		{
			$this->rules[$type] = [];
			$manager = $this->object();

			$filter = $manager->filter( true )->add( ['rule.type' => $type] )
				->order( 'rule.position' )->slice( 0, 10000 );

			foreach( $manager->search( $filter ) as $id => $ruleItem ) {
				$this->rules[$type][$id] = $manager->getProvider( $ruleItem, $type );
			}
		}

		foreach( $this->rules[$type] as $rule )
		{
			foreach( map( $items ) as $item )
			{
				if( $rule->apply( $item ) ) {
					return $items;
				}
			}
		}

		return $items;
	}


	/**
	 * Returns the rule provider which is responsible for the rule item.
	 *
	 * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
	 * @param string $type Rule type code
	 * @return \Aimeos\MShop\Rule\Provider\Iface Returns the decoratad rule provider object
	 * @throws \LogicException If provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Rule\Item\Iface $item, string $type ) : \Aimeos\MShop\Rule\Provider\Iface
	{
		$type = ucwords( $type );
		$context = $this->context();
		$names = explode( ',', $item->getProvider() );

		if( ctype_alnum( $type ) === false ) {
			throw new \LogicException( sprintf( 'Invalid characters in type name "%1$s"', $type ), 400 );
		}

		if( ( $provider = array_shift( $names ) ) === null ) {
			throw new \LogicException( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ), 400 );
		}

		if( ctype_alnum( $provider ) === false ) {
			throw new \LogicException( sprintf( 'Invalid characters in provider name "%1$s"', $provider ), 400 );
		}

		$classname = '\Aimeos\MShop\Rule\Provider\\' . $type . '\\' . $provider;
		$interface = \Aimeos\MShop\Rule\Provider\Factory\Iface::class;

		$provider = \Aimeos\Utils::create( $classname, [$context, $item], $interface );
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
		$context = $this->context();
		$classprefix = '\Aimeos\MShop\Rule\Provider\\' . $type . '\Decorator\\';

		foreach( $names as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$msg = $this->context()->translate( 'mshop', 'Invalid characters in class name "%1$s"' );
				throw new \Aimeos\MShop\Rule\Exception( sprintf( $msg, $name ), 400 );
			}

			$classname = $classprefix . $name;
			$interface = $classprefix . 'Iface';

			$provider = \Aimeos\Utils::create( $classname, [$context, $ruleItem, $provider], $interface );
		}

		return $provider;
	}
}
