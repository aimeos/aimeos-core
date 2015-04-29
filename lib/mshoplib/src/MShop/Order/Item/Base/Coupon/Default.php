<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Default implementation for order item base coupon.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Item_Base_Coupon_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Order_Item_Base_Coupon_Interface
{
	private $_values;

	/**
	 * Initializes the order base coupon item.
	 *
	 * @param array $values Values to be set on initialisation.
	 * Possible keys: 'id', 'baseid', 'ordprodid', 'code', 'mtime'
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct( 'order.base.coupon.', $values );

		$this->_values = $values;
	}


	/**
	 * Returns the base ID of the order.
	 *
	 * @return integer Order base Id.
	 */
	public function getBaseId()
	{
		return ( isset( $this->_values['baseid'] ) ? (int) $this->_values['baseid'] : null );
	}


	/**
	 * Sets the Base ID of the order.
	 *
	 * @param integer $baseid Order base ID.
	 */
	public function setBaseId( $baseid )
	{
		if ( $baseid == $this->getBaseId() ) { return; }

		$this->_values['baseid'] = (int) $baseid;
		$this->setModified();
	}


	/**
	 * 	Returns the ID of the ordered product.
	 *
	 *  @return integer ID of the ordered product.
	 */
	public function getProductId()
	{
		return ( isset( $this->_values['ordprodid'] ) ? (int) $this->_values['ordprodid'] : null );
	}


	/**
	 * 	Sets the ID of the ordered product.
	 *
	 * 	@param integer $productid ID of the ordered product
	 */
	public function setProductId( $productid )
	{
		if ( $productid == $this->getProductId() ) { return; }

		$this->_values['ordprodid'] = (int) $productid;
		$this->setModified();
	}


	/**
	 * Returns the coupon code.
	 *
	 * @return string Coupon code.
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : null );
	}


	/**
	 * Sets the coupon code.
	 *
	 * @param string $code Coupon code
	 */
	public function setCode( $code )
	{
		$this->_checkCode( $code );

		if ( $code == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $code;
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
				case 'order.base.coupon.baseid': $this->setBaseId( $value ); break;
				case 'order.base.coupon.productid': $this->setProductId( $value ); break;
				case 'order.base.coupon.code': $this->setCode( $value ); break;
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

		$list['order.base.coupon.baseid'] = $this->getBaseId();
		$list['order.base.coupon.productid'] = $this->getProductId();
		$list['order.base.coupon.code'] = $this->getCode();

		return $list;
	}

}
