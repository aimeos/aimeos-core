<?php
/**
	 * @copyright Metaways Infosystems GmbH, 2012
	 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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

	public function __construct( array $values = array( ) )
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
		return ( isset( $this->values['parentid'] ) ? (int) $this->values['parentid'] : null );
	}

	/**
	 * Sets the parentid of the order status.
	 *
	 * @param integer $parentid Parent ID of the order status
	 */
	public function setParentId( $parentid )
	{
		if( $parentid == $this->getParentId() ) {
			return;
		}

		$this->values['parentid'] = (int) $parentid;

		$this->setModified();
	}


	/**
	 * Returns the type of the order status.
	 *
	 * @return string Type of the order status
	 */
	public function getType()
	{
		return ( isset( $this->values['type'] ) ? (string) $this->values['type'] : '' );
	}

	/**
	 * Sets the type of the order status.
	 *
	 * @param string $type Type of the order status
	 */
	public function setType( $type )
	{
		if( $type == $this->getType() ) {
			return;
		}

		$this->values['type'] = (string) $type;

		$this->setModified();
	}

	/**
	 * Returns the value of the order status.
	 *
	 * @return string Value of the order status
	 */
	public function getValue()
	{
		return ( isset( $this->values['value'] ) ? (string) $this->values['value'] : '' );
	}

	/**
	 * Sets the value of the order status.
	 *
	 * @param string $value Value of the order status
	 */
	public function setValue( $value )
	{
		if( $value == $this->getValue() ) {
			return;
		}

		$this->values['value'] = (string) $value;

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
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{

		$list = parent::toArray();

		$list['order.status.parentid'] = $this->getParentId();
		$list['order.status.type'] = $this->getType();
		$list['order.status.value'] = $this->getValue();



		return $list;
	}

}