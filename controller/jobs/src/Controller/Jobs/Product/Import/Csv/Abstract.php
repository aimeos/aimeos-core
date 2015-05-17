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
	 * Returns the cache object for the given type
	 *
	 * @param string $type Type of the cached data
	 * @param string|null Name of the cache implementation
	 * @return Controller_Jobs_Product_Import_Csv_Cache_Interface Cache object
	 */
	protected function _getCache( $type, $name = null )
	{
		$context = $this->_getContext();
		$config = $context->getConfig();
		$iface = 'Controller_Jobs_Product_Import_Csv_Cache_Interface';

		if( $name === null ) {
			$name = $config->get( 'classes/controller/jobs/product/import/csv/cache/' . $type . '/name', 'Default' );
		}

		if( ctype_alnum( $type ) === false || ctype_alnum( $name ) === false )
		{
			$classname = is_string($name) ? 'Controller_Jobs_Product_Import_Csv_Cache_' . $type . '_' . $name : '<not a string>';
			throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$classname = 'Controller_Jobs_Product_Import_Csv_Cache_' . ucfirst( $type ) . '_' . $name;

		if( class_exists( $classname ) === false ) {
			throw new Controller_Jobs_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
		}

		$object = new $classname( $context );

		if( !( $object instanceof $iface ) ) {
			throw new Controller_Jobs_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		return $object;
	}


	/**
	 * Returns the processor object for saving the product related information
	 *
	 * @param array $mapping Associative list of processor types as keys and index/data mappings as values
	 * @return Controller_Jobs_Product_Import_Csv_Processor_Interface Processor object
	 */
	protected function _getProcessors( array $mappings )
	{
		$context = $this->_getContext();
		$config = $context->getConfig();
		$iface = 'Controller_Jobs_Product_Import_Csv_Processor_Interface';
		$object = new Controller_Jobs_Product_Import_Csv_Processor_Done( $context, array() );

		foreach( $mappings as $type => $mapping )
		{
			$name = $config->get( 'classes/controller/jobs/product/import/csv/processor/' . $type . '/name', 'Default' );

			if( ctype_alnum( $type ) === false )
			{
				$classname = is_string($name) ? 'Controller_Jobs_Product_Import_Csv_Processor_' . $type . '_' . $name : '<not a string>';
				throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
			}

			$classname = 'Controller_Jobs_Product_Import_Csv_Processor_' . ucfirst( $type ) . '_' . $name;

			if( class_exists( $classname ) === false ) {
				throw new Controller_Jobs_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$object = new $classname( $context, $mapping, $object );

			if( !( $object instanceof $iface ) ) {
				throw new Controller_Jobs_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
			}
		}

		return $object;
	}


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
		$search->setSlice( 0, count( $codes ) );

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
