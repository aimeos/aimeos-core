<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage JsonAdm
 */


namespace Aimeos\Controller\JsonAdm\Catalog;


/**
 * JSON API catalog controller
 *
 * @package Controller
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Controller\JsonAdm\Base
	implements \Aimeos\Controller\JsonAdm\Common\Iface
{
	/**
	 * Returns the list items for association relationships
	 *
	 * @param array $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( array $items, array $include )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog/lists' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.lists.parentid', array_keys( $items ) ),
			$search->compare( '==', 'catalog.lists.domain', $include ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $manager->searchItems( $search );
	}
}
