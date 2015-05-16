<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Common class for CSV product import job controllers and processors.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Import_Csv_Abstract
	extends Controller_Jobs_Abstract
{
	private static $_types = array();


	/**
	 * Returns the product items for the given codes
	 *
	 * @param array $codes List of product codes
	 * @param array $domains List of domains whose items should be fetched too
	 * @return array Associative list of product codes as key and product items as value
	 */
	protected function _getProducts( array $codes, array $domains )
	{
		$result = array();
		$manager = MShop_Factory::createManager( $this->_getContext(), 'product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $codes ) );

		foreach( $manager->searchItems( $search, $domains ) as $item ) {
			$result[ $item->getCode() ] = $item;
		}

		return $result;
	}


	/**
	 * Returns the ID of the type item with the given code
	 *
	 * @param string $path Item/manager path separated by slashes, e.g. "product/list/type"
	 * @param string $domain Domain the type items needs to be from
	 * @param string $code Unique code of the type item
	 * @return string Unique ID of the type item
	 */
	protected function _getTypeId( $path, $domain, $code )
	{
		if( !isset( self::$_types[$path][$domain] ) )
		{
			$manager = MShop_Factory::createManager( $this->_getContext(), $path );
			$key = str_replace( '/', '.', $path );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', $key . '.domain', $domain ) );

			foreach( $manager->searchItems( $search ) as $id => $item ) {
				self::$_types[$path][$domain][ $item->getCode() ] = $id;
			}
		}

		if( !isset( self::$_types[$path][$domain][$code] ) ) {
			throw new Controller_Jobs_Exception( sprintf( 'No type item for "%1$s/%2$s" in "%3$s" found', $domain, $code, $path ) );
		}

		return self::$_types[$path][$domain][$code];
	}
}
