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
class Controller_Common_Product_Import_Csv_Processor_Abstract
	extends Controller_Common_Product_Import_Csv_Abstract
{
	private static $_types = array();
	private $_context;
	private $_mapping;
	private $_object;


	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array $mapping Associative list of field position in CSV as key and domain item key as value
	 * @param Controller_Common_Product_Import_Csv_Processor_Interface $object Decorated processor
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $mapping,
		Controller_Common_Product_Import_Csv_Processor_Interface $object = null )
	{
		$this->_context = $context;
		$this->_mapping = $mapping;
		$this->_object = $object;
	}


	/**
	 * Adds the list item default values and returns the resulting array
	 *
	 * @param array $list Associative list of domain item keys and their values, e.g. "product.list.status" => 1
	 * @param integer $pos Computed position of the list item in the associated list of items
	 * @return array Given associative list enriched by default values if they were not already set
	 */
	protected function _addListItemDefaults( array $list, $pos )
	{
		if( !isset( $list['product.list.position'] ) ) {
			$list['product.list.position'] = $pos;
		}

		if( !isset( $list['product.list.status'] ) ) {
			$list['product.list.status'] = 1;
		}

		return $list;
	}


	/**
	 * Returns the context item
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _getContext()
	{
		return $this->_context;
	}


	/**
	 * Returns the mapping list
	 *
	 * @return array Associative list of field positions in CSV as keys and domain item keys as values
	 */
	protected function _getMapping()
	{
		return $this->_mapping;
	}


	/**
	 * Returns the decorated processor object
	 *
	 * @return Controller_Common_Product_Import_Csv_Processor_Interface Processor object
	 * @throws Controller_Jobs_Exception If no processor object is available
	 */
	protected function _getObject()
	{
		if( $this->_object === null ) {
			throw new Controller_Jobs_Exception( 'No processor object available' );
		}

		return $this->_object;
	}


	/**
	 * Returns the chunked data with text and product list properties in each chunk
	 *
	 * @param array $data List of CSV fields with position as key and domain item key as value
	 * @return array List of associative arrays containing the chunked properties
	 */
	protected function _getMappedChunk( array &$data )
	{
		$idx = 0;
		$map = array();

		foreach( $this->_getMapping() as $pos => $key )
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
