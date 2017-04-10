<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Status;


/**
 * Default implementation of the order status object.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Order\Item\Status\Base
	implements \Aimeos\MShop\Order\Item\Status\Iface
{
	private $values;


	/**
	 * Initializes the object
	 *
	 * @param array $values Associative list of key/value pairs with order status properties
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.status.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the parentid of the order status.
	 *
	 * @return integer|null Parent ID of the order
	 */
	public function getParentId()
	{
		if( isset( $this->values['order.status.parentid'] ) ) {
			return (int) $this->values['order.status.parentid'];
		}

		return null;
	}

	/**
	 * Sets the parentid of the order status.
	 *
	 * @param integer $parentid Parent ID of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setParentId( $parentid )
	{
		if( $parentid == $this->getParentId() ) { return $this; }

		$this->values['order.status.parentid'] = (int) $parentid;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the type of the order status.
	 *
	 * @return string Type of the order status
	 */
	public function getType()
	{
		if( isset( $this->values['order.status.type'] ) ) {
			return (string) $this->values['order.status.type'];
		}

		return '';
	}

	/**
	 * Sets the type of the order status.
	 *
	 * @param string $type Type of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setType( $type )
	{
		if( $type == $this->getType() ) { return $this; }

		$this->values['order.status.type'] = (string) $type;
		$this->setModified();

		return $this;
	}

	/**
	 * Returns the value of the order status.
	 *
	 * @return string Value of the order status
	 */
	public function getValue()
	{
		if( isset( $this->values['order.status.value'] ) ) {
			return (string) $this->values['order.status.value'];
		}

		return '';
	}

	/**
	 * Sets the value of the order status.
	 *
	 * @param string $value Value of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setValue( $value )
	{
		if( $value == $this->getValue() ) { return $this; }

		$this->values['order.status.value'] = (string) $value;
		$this->setModified();

		return $this;
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = [];
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.status.parentid': $this->setParentId( $value ); break;
				case 'order.status.type': $this->setType( $value ); break;
				case 'order.status.value': $this->setValue( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}



	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['order.status.type'] = $this->getType();
		$list['order.status.value'] = $this->getValue();

		if( $private === true ) {
			$list['order.status.parentid'] = $this->getParentId();
		}

		return $list;
	}

}