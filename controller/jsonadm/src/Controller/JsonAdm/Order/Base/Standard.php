<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage JsonAdm
 */


namespace Aimeos\Controller\JsonAdm\Order\Base;


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
		$this->getView()->assign( array( 'partial-data' => 'controller/jsonadm/partials/order/base/template-data' ) );

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
		$ids = array_keys( $items );
		$keys = array( 'order/base/address', 'order/base/coupon', 'order/base/product', 'order/base/service' );
		$include = array_intersect( $include, $keys );

		foreach( $include as $type )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $type );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', str_replace( '/', '.', $type ) . '.baseid', $ids ) );

			$list = array_merge( $list, $manager->searchItems( $search ) );
		}

		return $list;
	}
}
