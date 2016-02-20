<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage JsonAdm
 */


namespace Aimeos\Controller\JsonAdm\Product;


/**
 * JSON API product controller
 *
 * @package Controller
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Controller\JsonAdm\Base
	implements \Aimeos\Controller\JsonAdm\Common\Iface
{
	/** controller/jsonadm/product/decorators/excludes
	 * Excludes decorators added by the "common" option from the JSON API controllers
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "controller/jsonadm/common/decorators/default" before they are wrapped
	 * around the Jsonadm controller.
	 *
	 *  controller/jsonadm/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Controller\JsonAdm\Common\Decorator\*") added via
	 * "controller/jsonadm/common/decorators/default" for the JSON API controller.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see controller/jsonadm/common/decorators/default
	 * @see controller/jsonadm/product/decorators/global
	 * @see controller/jsonadm/product/decorators/local
	 */

	/** controller/jsonadm/product/decorators/global
	 * Adds a list of globally available decorators only to the Jsonadm controller
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Controller\Jsonadm\Common\Decorator\*") around the Jsonadm
	 * controller.
	 *
	 *  controller/jsonadm/product/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Controller\Jsonadm\Common\Decorator\Decorator1" only to the
	 * "product" Jsonadm controller.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see controller/jsonadm/common/decorators/default
	 * @see controller/jsonadm/product/decorators/excludes
	 * @see controller/jsonadm/product/decorators/local
	 */

	/** controller/jsonadm/product/decorators/local
	 * Adds a list of local decorators only to the Jsonadm controller
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Controller\Jsonadm\Product\Decorator\*") around the Jsonadm
	 * controller.
	 *
	 *  controller/jsonadm/product/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Controller\Jsonadm\Product\Decorator\Decorator2" only to the
	 * "product" Jsonadm controller.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see controller/jsonadm/common/decorators/default
	 * @see controller/jsonadm/product/decorators/excludes
	 * @see controller/jsonadm/product/decorators/global
	 */


	/**
	 * Returns the items with parent/child relationships
	 *
	 * @param array $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	protected function getChildItems( array $items, array $include )
	{
		$list = array();
		$prodIds = array_keys( $items );
		$include = array_intersect( $include, array( 'product/property', 'product/stock' ) );

		foreach( $include as $type )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $type );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', str_replace( '/', '.', $type ) . '.parentid', $prodIds ) );

			$list = array_merge( $list, $manager->searchItems( $search ) );
		}

		return $list;
	}


	/**
	 * Returns the list items for association relationships
	 *
	 * @param array $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( array $items, array $include )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/lists' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.lists.parentid', array_keys( $items ) ),
			$search->compare( '==', 'product.lists.domain', $include ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $manager->searchItems( $search );
	}
}
