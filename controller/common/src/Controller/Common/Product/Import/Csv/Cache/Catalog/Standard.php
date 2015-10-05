<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Product\Import\Csv\Cache\Catalog;


/**
 * Category cache for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard
	extends \Aimeos\Controller\Common\Product\Import\Csv\Cache\Base
	implements \Aimeos\Controller\Common\Product\Import\Csv\Cache\Iface
{
	private $categories = array();


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'catalog' );
		$result = $manager->searchItems( $manager->createSearch() );

		foreach( $result as $id => $item ) {
			$this->categories[ $item->getCode() ] = $id;
		}
	}


	/**
	 * Returns the catalog ID for the given code and type
	 *
	 * @param string $code Category code
	 * @param string|null $type Not used
	 * @return string|null Catalog ID or null if not found
	 */
	public function get( $code, $type = null )
	{
		if( isset( $this->categories[$code] ) ) {
			return $this->categories[$code];
		}

		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', $code ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) !== false )
		{
			$this->categories[$code] = $item->getId();
			return $item->getId();
		}
	}


	/**
	 * Adds the catalog item to the cache
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Catalog object
	 */
	public function set( \Aimeos\MShop\Common\Item\Iface $item )
	{
		$this->categories[ $item->getCode() ] = $item->getId();
	}
}