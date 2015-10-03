<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


/**
 * Common class for CSV product import job controllers and processors.
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Product_Import_Csv_Base
	extends Controller_Jobs_Base
{
	private static $types = array();


	/**
	 * Converts the CSV field data using the available converter objects
	 *
	 * @param array $convlist Associative list of CSV field indexes and converter objects
	 * @param array $data Associative list of product codes and lists of CSV field indexes and their data
	 * @return array Associative list of CSV field indexes and their converted data
	 */
	protected function convertData( array $convlist, array $data )
	{
		foreach( $convlist as $idx => $converter )
		{
			foreach( $data as $code => $list )
			{
				if( isset( $list[$idx] ) ) {
					$data[$code][$idx] = $converter->translate( $list[$idx] );
				}
			}
		}

		return $data;
	}


	/**
	 * Returns the cache object for the given type
	 *
	 * @param string $type Type of the cached data
	 * @param string|null Name of the cache implementation
	 * @return Controller_Common_Product_Import_Csv_Cache_Interface Cache object
	 */
	protected function getCache( $type, $name = null )
	{
		$context = $this->getContext();
		$config = $context->getConfig();
		$iface = 'Controller_Common_Product_Import_Csv_Cache_Interface';

		if( $name === null ) {
			$name = $config->get( 'classes/controller/common/product/import/csv/cache/' . $type . '/name', 'Default' );
		}

		if( ctype_alnum( $type ) === false || ctype_alnum( $name ) === false )
		{
			$classname = is_string($name) ? 'Controller_Common_Product_Import_Csv_Cache_' . $type . '_' . $name : '<not a string>';
			throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$classname = 'Controller_Common_Product_Import_Csv_Cache_' . ucfirst( $type ) . '_' . $name;

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
	 * Returns the list of converter objects based on the given converter map
	 *
	 * @param array $convmap List of converter names for the values at the position in the CSV file
	 * @return array Associative list of positions and converter objects
	 */
	protected function getConverterList( array $convmap )
	{
		$convlist = array();

		foreach( $convmap as $idx => $name ) {
			$convlist[$idx] = MW_Convert_Factory::createConverter( $name );
		}

		return $convlist;
	}


	/**
	 * Returns the rows from the CSV file up to the maximum count
	 *
	 * @param MW_Container_Content_Interface $content CSV content object
	 * @param integer $maxcnt Maximum number of rows that should be retrieved at once
	 * @return array List of arrays with product codes as keys and list of values from the CSV file
	 */
	protected function getData( MW_Container_Content_Interface $content, $maxcnt )
	{
		$count = 0;
		$data = array();

		while( $content->valid() && $count++ < $maxcnt )
		{
			$row = $content->current();
			$data[ $row[0] ] = $row;
			$content->next();
		}

		return $data;
	}


	/**
	 * Returns the default mapping for the CSV fields to the domain item keys
	 *
	 * Example:
	 *  'item' => array(
	 *  	0 => 'product.code', // e.g. unique EAN code
	 *  	1 => 'product.label', // UTF-8 encoded text, also used as product name
	 *  ),
	 *  'text' => array(
	 *  	3 => 'text.type', // e.g. "short" for short description
	 *  	4 => 'text.content', // UTF-8 encoded text
	 *  ),
	 *  'media' => array(
	 *  	5 => 'media.url', // relative URL of the product image on the server
	 *  ),
	 *  'price' => array(
	 *  	6 => 'price.value', // price with decimals separated by a dot, no thousand separator
	 *  	7 => 'price.taxrate', // tax rate with decimals separated by a dot
	 *  ),
	 *  'attribute' => array(
	 *  	8 => 'attribute.type', // e.g. "size", "length", "width", "color", etc.
	 *  	9 => 'attribute.code', // code of an existing attribute, new ones will be created automatically
	 *  ),
	 *  'product' => array(
	 *  	10 => 'product.code', // e.g. EAN code of another product
	 *  	11 => 'product.list.type', // e.g. "suggestion" for suggested product
	 *  ),
	 *  'property' => array(
	 *  	12 => 'product.property.type', // e.g. "package-weight"
	 *  	13 => 'product.property.value', // arbitrary value for the corresponding type
	 *  ),
	 *  'catalog' => array(
	 *  	14 => 'catalog.code', // e.g. Unique category code
	 *  	15 => 'catalog.list.type', // e.g. "promotion" for top seller products
	 *  ),
	 *
	 * @return array Associative list of domains as keys ("item" is special for the product itself) and a list of
	 * 	positions and the domain item keys as values.
	 */
	protected function getDefaultMapping()
	{
		return array(
			'item' => array(
				0 => 'product.code',
				1 => 'product.label',
				2 => 'product.type',
				3 => 'product.status',
			),
			'text' => array(
				4 => 'text.type',
				5 => 'text.content',
				6 => 'text.type',
				7 => 'text.content',
			),
			'media' => array(
				8 => 'media.url',
			),
			'price' => array(
				9 => 'price.quantity',
				10 => 'price.value',
				11 => 'price.taxrate',
			),
			'attribute' => array(
				12 => 'attribute.code',
				13 => 'attribute.type',
			),
			'product' => array(
				14 => 'product.code',
				15 => 'product.list.type',
			),
			'property' => array(
				16 => 'product.property.value',
				17 => 'product.property.type',
			),
			'catalog' => array(
				18 => 'catalog.code',
				19 => 'catalog.list.type',
			),
		);
	}


	/**
	 * Returns the mapped data from the CSV line
	 *
	 * @param array $mapping List of domain item keys with the CSV field position as key
	 * @param array $list List of CSV fields with the CSV field position as key
	 * @return Associative list of domain item keys and the converted values
	 */
	protected function getMappedData( array $mapping, array $list )
	{
		$map = array();

		foreach( $mapping as $idx => $key )
		{
			if( !isset( $list[$idx] ) ) {
				break;
			}

			$map[$key] = $list[$idx];
		}

		return $map;
	}


	/**
	 * Returns the processor object for saving the product related information
	 *
	 * @param array $mapping Associative list of processor types as keys and index/data mappings as values
	 * @return Controller_Common_Product_Import_Csv_Processor_Interface Processor object
	 */
	protected function getProcessors( array $mappings )
	{
		$context = $this->getContext();
		$config = $context->getConfig();
		$iface = 'Controller_Common_Product_Import_Csv_Processor_Interface';
		$object = new Controller_Common_Product_Import_Csv_Processor_Done( $context, array() );

		foreach( $mappings as $type => $mapping )
		{
			$name = $config->get( 'classes/controller/common/product/import/csv/processor/' . $type . '/name', 'Default' );

			if( ctype_alnum( $type ) === false )
			{
				$classname = is_string($name) ? 'Controller_Common_Product_Import_Csv_Processor_' . $type . '_' . $name : '<not a string>';
				throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
			}

			$classname = 'Controller_Common_Product_Import_Csv_Processor_' . ucfirst( $type ) . '_' . $name;

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
	protected function getProducts( array $codes, array $domains )
	{
		$result = array();
		$manager = MShop_Factory::createManager( $this->getContext(), 'product' );

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
	protected function getTypeId( $path, $domain, $code )
	{
		if( !isset( self::$types[$path][$domain] ) )
		{
			$manager = MShop_Factory::createManager( $this->getContext(), $path );
			$key = str_replace( '/', '.', $path );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', $key . '.domain', $domain ) );

			foreach( $manager->searchItems( $search ) as $id => $item ) {
				self::$types[$path][$domain][ $item->getCode() ] = $id;
			}
		}

		if( !isset( self::$types[$path][$domain][$code] ) ) {
			throw new Controller_Jobs_Exception( sprintf( 'No type item for "%1$s/%2$s" in "%3$s" found', $domain, $code, $path ) );
		}

		return self::$types[$path][$domain][$code];
	}
}
