<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Job controller for CSV product imports.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Import_Csv_Default
	extends Controller_Jobs_Product_Import_Csv_Abstract
	implements Controller_Jobs_Interface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Product import CSV' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Imports new and updates existing products from CSV files' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$config = $this->_getContext()->getConfig();

		/** controller/jobs/product/import/csv/domains
		 * List of item domain names that should be retrieved along with the product items
		 *
		 * @param array Associative list of MShop item domain names
		 * @since 2015.05
		 * @category Developer
		 * @see controller/jobs/product/import/csv/mapping
		 * @see controller/jobs/product/import/csv/converter
		 * @see controller/jobs/product/import/csv/max-size
		 */
		$default = array( 'attribute', 'media', 'price', 'product', 'text' );
		$domains = $config->get( 'controller/jobs/product/import/csv/domains', $default );

		/** controller/jobs/product/import/csv/mapping
		 * List of mappings between the position in the CSV file and item keys
		 *
		 * @param array Associative list of key/position pairs
		 * @since 2015.05
		 * @category Developer
		 * @see controller/jobs/product/import/csv/domains
		 * @see controller/jobs/product/import/csv/converter
		 * @see controller/jobs/product/import/csv/max-size
		 */
		$default = $this->_getDefaultMapping();
		$mappings = $config->get( 'controller/jobs/product/import/csv/mapping', $default );

		/** controller/jobs/product/import/csv/converter
		 * List of converter names for the values at the position in the CSV file
		 *
		 * @param array Associative list of position/converter name (or list of names) pairs
		 * @since 2015.05
		 * @category Developer
		 * @see controller/jobs/product/import/csv/domains
		 * @see controller/jobs/product/import/csv/mapping
		 * @see controller/jobs/product/import/csv/max-size
		 */
		$converters = $config->get( 'controller/jobs/product/import/csv/converter', array() );

		/** controller/jobs/product/import/csv/max-size
		 * Maximum number of CSV rows to import at once
		 *
		 * @param integer Number of rows
		 * @since 2015.05
		 * @category Developer
		 * @see controller/jobs/product/import/csv/domains
		 * @see controller/jobs/product/import/csv/mapping
		 * @see controller/jobs/product/import/csv/converter
		 */
		$maxcnt = $config->get( 'controller/jobs/product/import/csv/max-size', 1000 );


		if( !isset( $mappings['item'] ) || !is_array( $mappings['item'] ) )
		{
			$msg = sprintf( 'Required mapping key "%1$s" is missing or contains no array', 'item' );
			throw new Controller_Jobs_Exception( $msg );
		}

		$procMappings = $mappings;
		unset( $procMappings['item'] );

		$convlist = $this->_getConverterList( $converters );
		$processor = $this->_getProcessors( $procMappings );
		$container = $this->_getContainer();

		foreach( $container as $content )
		{
			while( ( $data = $this->_getData( $content, $maxcnt ) ) !== array() )
			{
				$data = $this->_convertData( $convlist, $data );
				$products = $this->_getProducts( array_keys( $data ), $domains );
				$this->_import( $products, $data, $mappings['item'], $processor );

				unset( $products, $data );
			}
		}

		$container->close();
	}


	/**
	 * Converts the CSV field data using the available converter objects
	 *
	 * @param array $convlist Associative list of CSV field indexes and converter objects
	 * @param array $list Associative list of CSV field indexes and their data
	 * @return array Associative list of CSV field indexes and their converted data
	 */
	protected function _convertData( array $convlist, array $list )
	{
		foreach( $convlist as $idx => $converter )
		{
			if( isset( $list[$idx] ) ) {
				$list[$idx] = $converter->translate( $list[$idx] );
			}
		}

		return $list;
	}


	/**
	 * Opens and returns the container which includes the product data
	 *
	 * @return MW_Container_Interface Container object
	 */
	protected function _getContainer()
	{
		$config = $this->_getContext()->getConfig();

		/** controller/jobs/product/import/csv/location
		 * File or directory where the content is stored which should be imported
		 *
		 * @param string Absolute file or directory path
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/import/csv/container/type
		 * @see controller/jobs/product/import/csv/container/content
		 * @see controller/jobs/product/import/csv/container/options
		*/
		$location = $config->get( 'controller/jobs/product/import/csv/location', '.' );

		/** controller/jobs/product/import/csv/container/type
		 * Nave of the container type to read the data from
		 *
		 * @param string Container type name
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/import/csv/location
		 * @see controller/jobs/product/import/csv/container/content
		 * @see controller/jobs/product/import/csv/container/options
		*/
		$container = $config->get( 'controller/jobs/product/import/csv/container/type', 'Directory' );

		/** controller/jobs/product/import/csv/container/content
		 * Name of the content type inside the container to read the data from
		 *
		 * @param array Content type name
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/import/csv/location
		 * @see controller/jobs/product/import/csv/container/type
		 * @see controller/jobs/product/import/csv/container/options
		*/
		$content = $config->get( 'controller/jobs/product/import/csv/container/content', 'CSV' );

		/** controller/jobs/product/import/csv/container/options
		 * List of file container options for the site map files
		 *
		 * @param array Associative list of option name/value pairs
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/import/csv/location
		 * @see controller/jobs/product/import/csv/container/content
		 * @see controller/jobs/product/import/csv/container/type
		*/
		$options = $config->get( 'controller/jobs/product/import/csv/container/options', array() );

		return MW_Container_Factory::getContainer( $location, $container, $content, $options );
	}


	/**
	 * Returns the list of converter objects based on the given converter map
	 *
	 * @param array $convmap List of converter names for the values at the position in the CSV file
	 * @return array Associative list of positions and converter objects
	 */
	protected function _getConverterList( array $convmap )
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
	protected function _getData( MW_Container_Content_Interface $content, $maxcnt )
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
	 *
	 * @return array Associative list of domains as keys ("item" is special for the product itself) and a list of
	 * 	positions and the domain item keys as values.
	 */
	protected function _getDefaultMapping()
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
				12 => 'attribute.type',
				13 => 'attribute.code',
			),
			'product' => array(
				14 => 'product.code',
				15 => 'product.list.type',
			),
			'property' => array(
				16 => 'product.property.type',
				17 => 'product.property.value',
			),
		);
	}


	/**
	 * Returns the mapped and converted data from the CSV line
	 * @param array $mapping List of domain item keys with the CSV field position as key
	 * @param array $list List of CSV fields with the CSV field position as key
	 * @return Associative list of domain item keys and the converted values
	 */
	protected function _getMappedData( array $mapping, array $list )
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
	 * Imports the CSV data and creates new products or updates existing ones
	 *
	 * @param array $products List of products items implementing MShop_Product_Item_Interface
	 * @param array $data Associative list of import data as index/value pairs
	 * @param array $mappings Associative list of positions and domain item keys
	 * @param Controller_Jobs_Product_Import_Csv_Processor_Interface $processor
	 * @throws Controller_Jobs_Exception
	 */
	protected function _import( array $products, array $data, array $mapping,
		Controller_Jobs_Product_Import_Csv_Processor_Interface $processor )
	{
		$context = $this->_getContext();
		$manager = MShop_Factory::createManager( $context, 'product' );

		foreach( $data as $code => $list )
		{
			$manager->begin();

			try
			{
				if( isset( $products[$code] ) ) {
					$product = $products[$code];
				} else {
					$product = $manager->createItem();
				}

				$map = $this->_getMappedData( $mapping, $list );

				$typecode = ( isset( $map['product.type'] ) ? $map['product.type'] : 'default' );
				$map['product.typeid'] = $this->_getTypeId( 'product/type', 'product', $typecode );

				$product->fromArray( $map );
				$manager->saveItem( $product );

				$remaining = $processor->process( $product, $list );

				if( !empty( $remaining ) ) {
					$context->getLogger()->log( 'Not imported: ' . print_r( $remaining, true ) );
				}

				$manager->commit();
			}
			catch( Exception $e )
			{
				$msg = sprintf( 'Unable to import product with code "%1$s": %2$s', $code, $e->getMessage() );
				$context->getLogger()->log( $msg );

				$manager->rollback();
			}
		}
	}
}
