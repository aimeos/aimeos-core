<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
 */


/**
 * Default product stock item implementation.
 *
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Item_Stock_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Product_Item_Stock_Interface
{
	private $_values;

	/**
	 * Initializes the stock item object with the given values
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct('product.stock.', $values);

		$this->_values = $values;
	}


	/**
	 * Returns the product Id.
	 *
	 * @return integer Product Id
	 */
	public function getProductId()
	{
		return ( isset( $this->_values['prodid'] ) ? (int) $this->_values['prodid'] : null );
	}


	/**
	 * Sets the Product Id.
	 *
	 * @param integer $prodid New product Id
	 */
	public function setProductId( $prodid )
	{
		if ( $prodid == $this->getProductId() ) { return; }

		$this->_values['prodid'] = (int) $prodid;
		$this->setModified();
	}


	/**
	 * Returns the warehouse Id.
	 *
	 * @return integer Warehouse Id
	 */
	public function getWarehouseId()
	{
		return ( isset( $this->_values['warehouseid'] ) ? (int) $this->_values['warehouseid'] : null );
	}


	/**
	 * Sets the warehouse Id.
	 *
	 * @param integer|null $warehouseid New warehouse Id
	 */
	public function setWarehouseId( $warehouseid )
	{
		if ( $warehouseid === $this->getWarehouseId() ) { return; }

		if ( $warehouseid !== null ) {
			$warehouseid = (int) $warehouseid;
		}

		$this->_values['warehouseid'] = $warehouseid;
		$this->setModified();
	}


	/**
	 * Returns the stock level.
	 *
	 * @return integer Stock level
	 */
	public function getStocklevel()
	{
		return ( isset( $this->_values['stocklevel'] ) ? (int) $this->_values['stocklevel'] : null );
	}


	/**
	 * Sets the stock level.
	 *
	 * @param integer|null $stocklevel New stock level
	 */
	public function setStocklevel( $stocklevel )
	{
		if ( $stocklevel === $this->getStocklevel() ) { return; }

		if ( $stocklevel !== null ) {
			$stocklevel = (int) $stocklevel;
		}

		$this->_values['stocklevel'] = $stocklevel;
		$this->setModified();
	}


	/**
	 * Returns the back in stock date of the product.
	 *
	 * @return string Back in stock date of the product
	 */
	public function getDateBack()
	{
		return ( isset( $this->_values['backdate'] ) ? (string) $this->_values['backdate'] : null );
	}


	/**
	 * Sets the product back in stock date.
	 *
	 * @param string|null $backdate New back in stock date of the product
	 */
	public function setDateBack( $backdate )
	{
		if ( $backdate === $this->getDateBack() ) { return; }

		$this->_checkDateFormat($backdate);

		if ( $backdate !== null ) {
			$backdate = (string) $backdate;
		}

		$this->_values['backdate'] = $backdate;
		$this->setModified();
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['product.stock.productid'] = $this->getProductId();
		$list['product.stock.warehouseid'] = $this->getWarehouseId();
		$list['product.stock.stocklevel'] = $this->getStocklevel();
		$list['product.stock.dateback'] = $this->getDateBack();

		return $list;
	}

}