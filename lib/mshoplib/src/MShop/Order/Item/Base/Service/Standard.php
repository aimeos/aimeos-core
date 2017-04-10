<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Service;


/**
 * Default implementation for order item base service.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends Base implements Iface
{
	private $price;
	private $values;


	/**
	 * Initializes the order base service item
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price
	 * @param array $values Values to be set on initialisation
	 * @param array $attributes Attributes to be set on initialisation
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, array $values = [], array $attributes = [] )
	{
		parent::__construct( $price, $values, $attributes );

		$this->values = $values;
		$this->price = $price;
	}


	/**
	 * Clones internal objects of the order base service item.
	 */
	public function __clone()
	{
		$this->price = clone $this->price;
	}


	/**
	 * Returns the order base ID of the order service if available.
	 *
	 * @return integer|null Base ID of the item.
	 */
	public function getBaseId()
	{
		if( isset( $this->values['order.base.service.baseid'] ) ) {
			return (int) $this->values['order.base.service.baseid'];
		}

		return null;
	}


	/**
	 * Sets the order service base ID of the order service item.
	 *
	 * @param integer|null $value Order service base ID
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setBaseId( $value )
	{
		if( $value == $this->getBaseId() ) { return $this; }

		$this->values['order.base.service.baseid'] = ( $value !== null ? (int) $value : null );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the original ID of the service item used for the order.
	 *
	 * @return string Original service ID
	 */
	public function getServiceId()
	{
		if( isset( $this->values['order.base.service.serviceid'] ) ) {
			return (string) $this->values['order.base.service.serviceid'];
		}

		return '';
	}


	/**
	 * Sets a new ID of the service item used for the order.
	 *
	 * @param string $servid ID of the service item used for the order
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setServiceId( $servid )
	{
		if( $servid == $this->getServiceId() ) { return $this; }

		$this->values['order.base.service.serviceid'] = (string) $servid;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the code of the service item.
	 *
	 * @return string Service item code
	 */
	public function getCode()
	{
		if( isset( $this->values['order.base.service.code'] ) ) {
			return (string) $this->values['order.base.service.code'];
		}

		return '';
	}


	/**
	 * Sets a new code for the service item.
	 *
	 * @param string $code Code as defined by the service provider
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setCode( $code )
	{
		if( $code == $this->getCode() ) { return $this; }

		$this->values['order.base.service.code'] = (string) $this->checkCode( $code );;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the name of the service item.
	 *
	 * @return string Service item name
	 */
	public function getName()
	{
		if( isset( $this->values['order.base.service.name'] ) ) {
			return (string) $this->values['order.base.service.name'];
		}

		return '';
	}


	/**
	 * Sets a new name for the service item.
	 *
	 * @param string $name service item name
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setName( $name )
	{
		if( $name == $this->getName() ) { return $this; }

		$this->values['order.base.service.name'] = (string) $name;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the type of the service item.
	 *
	 * @return string service item type
	 */
	public function getType()
	{
		if( isset( $this->values['order.base.service.type'] ) ) {
			return (string) $this->values['order.base.service.type'];
		}

		return '';
	}


	/**
	 * Sets a new type for the service item.
	 *
	 * @param string $type Type of the service item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setType( $type )
	{
		if( $type == $this->getType() ) { return $this; }

		$this->values['order.base.service.type'] = (string) $type;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl()
	{
		if( isset( $this->values['order.base.service.mediaurl'] ) ) {
			return (string) $this->values['order.base.service.mediaurl'];
		}

		return '';
	}


	/**
	 * Sets the media url of the service item.
	 *
	 * @param string $value Location of the media/picture
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setMediaUrl( $value )
	{
		if( $value == $this->getMediaUrl() ) { return $this; }

		$this->values['order.base.service.mediaurl'] = (string) $value;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the price object which belongs to the service item.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item
	 */
	public function getPrice()
	{
		return $this->price;
	}


	/**
	 * Sets a new price object for the service item.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setPrice( \Aimeos\MShop\Price\Item\Iface $price )
	{
		if( $price === $this->price ) { return $this; }

		$this->price = $price;
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
				case 'order.base.service.baseid': $this->setBaseId( $value ); break;
				case 'order.base.service.code': $this->setCode( $value ); break;
				case 'order.base.service.serviceid': $this->setServiceId( $value ); break;
				case 'order.base.service.name': $this->setName( $value ); break;
				case 'order.base.service.mediaurl': $this->setMediaUrl( $value ); break;
				case 'order.base.service.type': $this->setType( $value ); break;
				case 'order.base.service.price': $this->price->setValue( $value ); break;
				case 'order.base.service.costs': $this->price->setCosts( $value ); break;
				case 'order.base.service.rebate': $this->price->setRebate( $value ); break;
				case 'order.base.service.taxrate': $this->price->setTaxRate( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values.
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$price = $this->price;

		$list['order.base.service.code'] = $this->getCode();
		$list['order.base.service.serviceid'] = $this->getServiceId();
		$list['order.base.service.name'] = $this->getName();
		$list['order.base.service.mediaurl'] = $this->getMediaUrl();
		$list['order.base.service.type'] = $this->getType();
		$list['order.base.service.price'] = $price->getValue();
		$list['order.base.service.costs'] = $price->getCosts();
		$list['order.base.service.rebate'] = $price->getRebate();
		$list['order.base.service.taxrate'] = $price->getTaxRate();

		if( $private === true ) {
			$list['order.base.service.baseid'] = $this->getBaseId();
		}

		return $list;
	}


	/**
	 * Copys all data from a given service item.
	 *
	 * @param \Aimeos\MShop\Service\Item\Iface $service New service item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Service\Item\Iface $service )
	{
		$this->setCode( $service->getCode() );
		$this->setName( $service->getName() );
		$this->setType( $service->getType() );
		$this->setServiceId( $service->getId() );

		$items = $service->getRefItems( 'media', 'default', 'default' );

		if( ( $item = reset( $items ) ) !== false ) {
			$this->setMediaUrl( $item->getUrl() );
		}

		$this->setModified();

		return $this;
	}
}