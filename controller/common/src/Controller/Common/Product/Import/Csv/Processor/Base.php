<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


/**
 * Abstract class with common methods for all CSV import processors
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Product_Import_Csv_Processor_Base
	extends Controller_Common_Product_Import_Csv_Base
{
	private static $types = array();
	private $context;
	private $mapping;
	private $object;


	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Iface $context Context object
	 * @param array $mapping Associative list of field position in CSV as key and domain item key as value
	 * @param Controller_Common_Product_Import_Csv_Processor_Iface $object Decorated processor
	 */
	public function __construct( MShop_Context_Item_Iface $context, array $mapping,
		Controller_Common_Product_Import_Csv_Processor_Iface $object = null )
	{
		$this->context = $context;
		$this->mapping = $mapping;
		$this->object = $object;
	}


	/**
	 * Adds the list item default values and returns the resulting array
	 *
	 * @param array $list Associative list of domain item keys and their values, e.g. "product.lists.status" => 1
	 * @param integer $pos Computed position of the list item in the associated list of items
	 * @return array Given associative list enriched by default values if they were not already set
	 */
	protected function addListItemDefaults( array $list, $pos )
	{
		if( !isset( $list['product.lists.position'] ) ) {
			$list['product.lists.position'] = $pos;
		}

		if( !isset( $list['product.lists.status'] ) ) {
			$list['product.lists.status'] = 1;
		}

		return $list;
	}


	/**
	 * Returns the context item
	 *
	 * @return MShop_Context_Item_Iface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the mapping list
	 *
	 * @return array Associative list of field positions in CSV as keys and domain item keys as values
	 */
	protected function getMapping()
	{
		return $this->mapping;
	}


	/**
	 * Returns the decorated processor object
	 *
	 * @return Controller_Common_Product_Import_Csv_Processor_Iface Processor object
	 * @throws Controller_Jobs_Exception If no processor object is available
	 */
	protected function getObject()
	{
		if( $this->object === null ) {
			throw new Controller_Jobs_Exception( 'No processor object available' );
		}

		return $this->object;
	}


	/**
	 * Returns the chunked data with text and product list properties in each chunk
	 *
	 * @param array $data List of CSV fields with position as key and domain item key as value
	 * @return array List of associative arrays containing the chunked properties
	 */
	protected function getMappedChunk( array &$data )
	{
		$idx = 0;
		$map = array();

		foreach( $this->getMapping() as $pos => $key )
		{
			if( isset( $map[$idx][$key] ) ) {
				$idx++;
			}

			if( isset( $data[$pos] ) )
			{
				$map[$idx][$key] = $data[$pos];
				unset( $data[$pos] );
			}
		}

		return $map;
	}
}
