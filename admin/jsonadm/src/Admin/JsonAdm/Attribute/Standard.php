<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Attribute;


/**
 * JSON API attribute client
 *
 * @package Controller
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Admin\JsonAdm\Base
	implements \Aimeos\Admin\JsonAdm\Common\Iface
{
	/** admin/jsonadm/attribute/decorators/excludes
	 * Excludes decorators added by the "common" option from the JSON API clients
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "admin/jsonadm/common/decorators/default" before they are wrapped
	 * around the Jsonadm client.
	 *
	 *  admin/jsonadm/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Admin\JsonAdm\Common\Decorator\*") added via
	 * "admin/jsonadm/common/decorators/default" for the JSON API client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/attribute/decorators/global
	 * @see admin/jsonadm/attribute/decorators/local
	 */

	/** admin/jsonadm/attribute/decorators/global
	 * Adds a list of globally available decorators only to the Jsonadm client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Controller\Jsonadm\Common\Decorator\*") around the Jsonadm
	 * client.
	 *
	 *  admin/jsonadm/attribute/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Controller\Jsonadm\Common\Decorator\Decorator1" only to the
	 * "attribute" Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/attribute/decorators/excludes
	 * @see admin/jsonadm/attribute/decorators/local
	 */

	/** admin/jsonadm/attribute/decorators/local
	 * Adds a list of local decorators only to the Jsonadm client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Controller\Jsonadm\Attribute\Decorator\*") around the Jsonadm
	 * client.
	 *
	 *  admin/jsonadm/attribute/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Controller\Jsonadm\Attribute\Decorator\Decorator2" only to the
	 * "attribute" Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/attribute/decorators/excludes
	 * @see admin/jsonadm/attribute/decorators/global
	 */


	/**
	 * Returns the list items for association relationships
	 *
	 * @param array $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( array $items, array $include )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'attribute/lists' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.lists.parentid', array_keys( $items ) ),
			$search->compare( '==', 'attribute.lists.domain', $include ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $manager->searchItems( $search );
	}
}
