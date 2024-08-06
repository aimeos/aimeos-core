<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2024
 * @package MShop
 * @subpackage Group
 */


namespace Aimeos\MShop\Group\Manager;


/**
 * Default implementation of the group manager
 *
 * @package MShop
 * @subpackage Group
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Group\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/** mshop/group/manager/name
	 * Class name of the used group manager implementation
	 *
	 * Each default manager can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Group\Manager\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Group\Manager\Mymanager
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/group/manager/name = Mymanager
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
	 * @since 2024.04
	 * @category Developer
	 */

	/** mshop/group/manager/decorators/excludes
	 * Excludes decorators added by the "common" option from the group manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the group manager.
	 *
	 *  mshop/group/manager/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the group manager.
	 *
	 * @param array List of decorator names
	 * @since 2024.04
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/group/manager/decorators/global
	 * @see mshop/group/manager/decorators/local
	 */

	/** mshop/group/manager/decorators/global
	 * Adds a list of globally available decorators only to the group manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the group
	 * manager.
	 *
	 *  mshop/group/manager/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the
	 * group manager.
	 *
	 * @param array List of decorator names
	 * @since 2024.04
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/group/manager/decorators/excludes
	 * @see mshop/group/manager/decorators/local
	 */

	/** mshop/group/manager/decorators/local
	 * Adds a list of local decorators only to the group manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Group\Manager\Decorator\*") around the group
	 * manager.
	 *
	 *  mshop/group/manager/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Group\Manager\Decorator\Decorator2" only to the
	 * group manager.
	 *
	 * @param array List of decorator names
	 * @since 2024.04
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/group/manager/decorators/excludes
	 * @see mshop/group/manager/decorators/global
	 */


	 private array $searchConfig = [
		'code' => [
			'code' => 'group.code',
			'internalcode' => 'mgro."code"',
			'label' => 'Group code',
		],
		'label' => [
			'code' => 'group.label',
			'internalcode' => 'mgro."label"',
			'label' => 'Group label',
		],
	];


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Group\Item\Iface New group item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['group.siteid'] = $values['group.siteid'] ?? $this->context()->locale()->getSiteId();
		return new \Aimeos\MShop\Group\Item\Standard( 'group.', $values );
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function find( string $code, array $ref = [], string $domain = null, string $type = null,
		?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->findBase( ['group.code' => $code], $ref, $default );
	}


	/**
	 * Returns the additional column/search definitions
	 *
	 * @return array Associative list of column names as keys and items implementing \Aimeos\Base\Criteria\Attribute\Iface
	 */
	public function getSaveAttributes() : array
	{
		return $this->createAttributes( $this->searchConfig );
	}


	/**
	 * Returns the prefix for the item properties and search keys.
	 *
	 * @return string Prefix for the item properties and search keys
	 */
	protected function getPrefix() : string
	{
		return 'group.';
	}
}
