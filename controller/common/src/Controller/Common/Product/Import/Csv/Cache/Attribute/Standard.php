<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Product\Import\Csv\Cache\Attribute;


/**
 * Attribute cache for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard
	extends \Aimeos\Controller\Common\Product\Import\Csv\Cache\Base
	implements \Aimeos\Controller\Common\Product\Import\Csv\Cache\Iface
{
	private $attributes = array();


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
		$result = $manager->searchItems( $manager->createSearch() );

		foreach( $result as $id => $item ) {
			$this->attributes[ $item->getCode() ][ $item->getType() ] = $item;
		}
	}


	/**
	 * Returns the attribute item for the given code and type
	 *
	 * @param string $code Attribute code
	 * @param string|null $type Attribute type
	 * @return \Aimeos\MShop\Attribute\Item\Iface|null Attribute object or null if not found
	 */
	public function get( $code, $type = null )
	{
		if( isset( $this->attributes[$code] ) && isset( $this->attributes[$code][$type] ) ) {
			return $this->attributes[$code][$type];
		}

		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'attribute' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', $code ),
			$search->compare( '==', 'attribute.type.code', $type ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) !== false )
		{
			$this->attributes[$code][$type] = $item;
			return $item;
		}
	}


	/**
	 * Adds the attribute item to the cache
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Attribute object
	 */
	public function set( \Aimeos\MShop\Common\Item\Iface $item )
	{
		$code = $item->getCode();

		if( !isset( $this->attributes[$code] ) || !is_array( $this->attributes[$code] ) ) {
			$this->attributes[$code] = array();
		}

		$this->attributes[$code][ $item->getType() ] = $item;
	}
}