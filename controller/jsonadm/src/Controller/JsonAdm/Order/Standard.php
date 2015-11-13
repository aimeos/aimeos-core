<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage JsonAdm
 */


namespace Aimeos\Controller\JsonAdm\Order;


/**
 * JSON API order controller
 *
 * @package Controller
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Controller\JsonAdm\Base
	implements \Aimeos\Controller\JsonAdm\Common\Iface
{
	/**
	 * Returns the requested resource or the resource list
	 *
	 * @param string $body Request body
	 * @param string &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function get( $body, array &$header, &$status )
	{
		$this->getView()->assign( array( 'partial-data' => 'controller/jsonadm/partials/order/template-data' ) );

		return parent::get( $body, $header, $status );
	}


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

		if( in_array( 'order/base', $include ) )
		{
			$ids = array();

			foreach( $items as $item ) {
				$ids[] = $item->getBaseId();
			}

			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'order/base' );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', 'order.base.id', $ids ) );

			$list = array_merge( $list, $manager->searchItems( $search ) );
		}

		if( in_array( 'order/status', $include ) )
		{
			$ids = array_keys( $items );
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'order/status' );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', 'order.status.parentid', $ids ) );

			$list = array_merge( $list, $manager->searchItems( $search ) );
		}

		return $list;
	}
}
