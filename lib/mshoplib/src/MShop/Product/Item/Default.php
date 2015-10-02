<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Product
 */


/**
 * Default impelementation of a product item.
 *
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Item_Default
	extends MShop_Common_Item_ListRef_Abstract
	implements MShop_Product_Item_Interface
{
	private $values;


	/**
	 * Initializes the item object.
	 *
	 * @param array $values Parameter for initializing the basic properties
	 * @param MShop_Common_List_Item_Interface[] $listItems List of list items
	 * @param MShop_Common_Item_Interface[] $refItems List of referenced items
	 */
	public function __construct( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		parent::__construct( 'product.', $values, $listItems, $refItems );

		$this->values = $values;
	}


	/**
	 * Returns the type ID of the product item.
	 *
	 * @return integer|null Type ID of the product item
	 */
	public function getTypeId()
	{
		return ( isset( $this->values['typeid'] ) ? (int) $this->values['typeid'] : null );
	}


	/**
	 * Sets the new type ID of the product item.
	 *
	 * @param integer $typeid New type ID of the product item
	 */
	public function setTypeId( $typeid )
	{
		if( $typeid == $this->getTypeId() ) { return; }

		$this->values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the type of the product item.
	 *
	 * @return string|null Type of the product item
	 */
	public function getType()
	{
		return ( isset( $this->values['type'] ) ? (string) $this->values['type'] : null );
	}


	/**
	 * Returns the status of the product item.
	 *
	 * @return integer Status of the product item
	 */
	public function getStatus()
	{
		return ( isset( $this->values['status'] ) ? (int) $this->values['status'] : 0 );
	}


	/**
	 * Sets the new status of the product item.
	 *
	 * @param integer $status New status of the product item
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return; }

		$this->values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the code of the product item.
	 *
	 * @return string Code of the product item
	 */
	public function getCode()
	{
		return ( isset( $this->values['code'] ) ? (string) $this->values['code'] : '' );
	}


	/**
	 * Sets the new code of the product item.
	 *
	 * @param string $code New code of product item
	 */
	public function setCode( $code )
	{
		$this->checkCode( $code );

		if( $code == $this->getCode() ) { return; }

		$this->values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the supplier code of the product item.
	 *
	 * @return string supplier code of the product item
	 */
	public function getSupplierCode()
	{
		return ( isset( $this->values['suppliercode'] ) ? (string) $this->values['suppliercode'] : '' );
	}


	/**
	 * Sets the new supplier code of the product item.
	 *
	 * @param string $suppliercode New supplier code of the product item
	 */
	public function setSupplierCode( $suppliercode )
	{
		if( $suppliercode == $this->getSupplierCode() ) { return; }

		$this->values['suppliercode'] = (string) $suppliercode;
		$this->setModified();
	}


	/**
	 * Returns the label of the product item.
	 *
	 * @return string Label of the product item
	 */
	public function getLabel()
	{
		return ( isset( $this->values['label'] ) ? (string) $this->values['label'] : '' );
	}


	/**
	 * Sets a new label of the product item.
	 *
	 * @param string $label New label of the product item
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return; }

		$this->values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the starting point of time, in which the product is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart()
	{
		return ( isset( $this->values['start'] ) ? (string) $this->values['start'] : null );
	}


	/**
	 * Sets a new starting point of time, in which the product is available.
	 *
	 * @param string|null New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateStart( $date )
	{
		if( $date === $this->getDateStart() ) { return; }

		$this->checkDateFormat( $date );

		$this->values['start'] = ( $date !== null ? (string) $date : null );
		$this->setModified();
	}


	/**
	 * Returns the ending point of time, in which the product is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd()
	{
		return ( isset( $this->values['end'] ) ? (string) $this->values['end'] : null );
	}


	/**
	 * Sets a new ending point of time, in which the product is available.
	 *
	 * @param string|null New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateEnd( $date )
	{
		if( $date === $this->getDateEnd() ) { return; }

		$this->checkDateFormat( $date );

		$this->values['end'] = ( $date !== null ? (string) $date : null );
		$this->setModified();
	}


	/**
	 * Returns the configuration values of the item
	 *
	 * @return array Configuration values
	 */
	public function getConfig()
	{
		return ( isset( $this->values['config'] ) ? $this->values['config'] : array() );
	}


	/**
	 * Sets the configuration values of the item.
	 *
	 * @param array $config Configuration values
	 */
	public function setConfig( array $config )
	{
		$this->values['config'] = $config;
		$this->setModified();
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'product.typeid': $this->setTypeId( $value ); break;
				case 'product.code': $this->setCode( $value ); break;
				case 'product.label': $this->setLabel( $value ); break;
				case 'product.status': $this->setStatus( $value ); break;
				case 'product.suppliercode': $this->setSupplierCode( $value ); break;
				case 'product.datestart': $this->setDateStart( $value ); break;
				case 'product.dateend': $this->setDateEnd( $value ); break;
				case 'product.config': $this->setConfig( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['product.typeid'] = $this->getTypeId();
		$list['product.type'] = $this->getType();
		$list['product.code'] = $this->getCode();
		$list['product.label'] = $this->getLabel();
		$list['product.status'] = $this->getStatus();
		$list['product.suppliercode'] = $this->getSupplierCode();
		$list['product.datestart'] = $this->getDateStart();
		$list['product.dateend'] = $this->getDateEnd();
		$list['product.config'] = $this->getConfig();

		return $list;
	}
}
