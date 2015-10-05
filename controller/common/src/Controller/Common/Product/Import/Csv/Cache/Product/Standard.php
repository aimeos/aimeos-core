<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Product\Import\Csv\Cache\Product;


/**
 * Product cache for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard
	extends \Aimeos\Controller\Common\Product\Import\Csv\Cache\Base
	implements \Aimeos\Controller\Common\Product\Import\Csv\Cache\Iface
{
	private $prodmap = array();


	/**
	 * Returns the product ID for the given code
	 *
	 * @param string $code Product code
	 * @param string|null $type Attribute type
	 * @return string|null Product ID or null if not found
	 */
	public function get( $code, $type = null )
	{
		if( isset( $this->prodmap[$code] ) ) {
			return $this->prodmap[$code];
		}

		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) !== false )
		{
			$this->prodmap[$code] = $item->getId();
			return $this->prodmap[$code];
		}
	}


	/**
	 * Adds the product ID to the cache
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Product object
	 */
	public function set( \Aimeos\MShop\Common\Item\Iface $item )
	{
		$this->prodmap[ $item->getCode() ] = $item->getId();
	}
}