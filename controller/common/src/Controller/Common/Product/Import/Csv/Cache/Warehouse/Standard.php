<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Product\Import\Csv\Cache\Warehouse;


/**
 * Warehouse cache for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard
	extends \Aimeos\Controller\Common\Product\Import\Csv\Cache\Base
	implements \Aimeos\Controller\Common\Product\Import\Csv\Cache\Iface
{
	private $warehouses = array();


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'product/stock/warehouse' );
		$search = $manager->createSearch();
		$search->setSlice( 0, 1000 );

		foreach( $manager->searchItems( $search ) as $id => $item ) {
			$this->warehouses[ $item->getCode() ] = $id;
		}
	}


	/**
	 * Returns the warehouse ID for the given code
	 *
	 * @param string $code Warehouse code
	 * @param string|null $type Attribute type
	 * @return string|null Warehouse ID or null if not found
	 */
	public function get( $code, $type = null )
	{
		if( isset( $this->warehouses[$code] ) ) {
			return $this->warehouses[$code];
		}
	}


	/**
	 * Adds the warehouse ID to the cache
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Warehouse object
	 */
	public function set( \Aimeos\MShop\Common\Item\Iface $item )
	{
		$this->warehouses[ $item->getCode() ] = $item->getId();
	}
}