<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Service;


/**
 * Default order service item implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends Base implements Iface
{
	use \Aimeos\MShop\Common\Item\TypeRef\Traits;


	/**
	 * Clones internal objects of the order product item.
	 */
	public function __clone()
	{
		$this->set( '.transactions', map( $this->get( '.transactions', [] ) )->clone() );
		$this->set( '.attributes', map( $this->get( '.attributes', [] ) )->clone() );
		$this->set( '.price', clone $this->get( '.price' ) );

		parent::__clone();
	}


	/**
	 * Returns the price item for the service.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate
	 */
	public function getPrice() : \Aimeos\MShop\Price\Item\Iface
	{
		return $this->get( '.price' );
	}


	/**
	 * Sets the price item for the service.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item containing price and additional costs
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function setPrice( \Aimeos\MShop\Price\Item\Iface $price ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $this->set( '.price', $price );
	}


	/**
	 * Returns the associated service item
	 *
	 * @return \Aimeos\MShop\Service\Item\Iface|null Service item
	 */
	public function getServiceItem() : ?\Aimeos\MShop\Service\Item\Iface
	{
		return $this->get( '.service' );
	}


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return string Site ID (or null if not available)
	 */
	public function getSiteId() : string
	{
		return $this->get( 'order.service.siteid', '' );
	}


	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $this->set( 'order.service.siteid', $value );
	}


	/**
	 * Returns the order base ID of the order service if available.
	 *
	 * @return string|null Base ID of the item.
	 */
	public function getParentId() : ?string
	{
		return $this->get( 'order.service.parentid' );
	}


	/**
	 * Sets the order service base ID of the order service item.
	 *
	 * @param string|null $value Order service base ID
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function setParentId( ?string $value ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $this->set( 'order.service.parentid', $value );
	}


	/**
	 * Returns the original ID of the service item used for the order.
	 *
	 * @return string Original service ID
	 */
	public function getServiceId() : string
	{
		return $this->get( 'order.service.serviceid', '' );
	}


	/**
	 * Sets a new ID of the service item used for the order.
	 *
	 * @param string $servid ID of the service item used for the order
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function setServiceId( string $servid ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $this->set( 'order.service.serviceid', $servid );
	}


	/**
	 * Returns the code of the service item.
	 *
	 * @return string Service item code
	 */
	public function getCode() : string
	{
		return $this->get( 'order.service.code', '' );
	}


	/**
	 * Sets a new code for the service item.
	 *
	 * @param string $code Code as defined by the service provider
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $this->set( 'order.service.code', $this->checkCode( $code ) );
	}


	/**
	 * Returns the name of the service item.
	 *
	 * @return string Service item name
	 */
	public function getName() : string
	{
		return $this->get( 'order.service.name', '' );
	}


	/**
	 * Sets a new name for the service item.
	 *
	 * @param string $name service item name
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function setName( string $name ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $this->set( 'order.service.name', $name );
	}


	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl() : string
	{
		return $this->get( 'order.service.mediaurl', '' );
	}


	/**
	 * Sets the media url of the service item.
	 *
	 * @param string $value Location of the media/picture
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function setMediaUrl( string $value ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $this->set( 'order.service.mediaurl', $value );
	}


	/**
	 * Returns the position of the service in the order.
	 *
	 * @return int|null Service position in the order from 0-n
	 */
	public function getPosition() : ?int
	{
		return $this->get( 'order.service.position' );
	}


	/**
	 * Sets the position of the service within the list of ordered servicees
	 *
	 * @param int|null $value Service position in the order from 0-n or null for resetting the position
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If the position is invalid
	 */
	public function setPosition( ?int $value ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		if( $value < 0 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order service position "%1$s" must be greater than 0', $value ) );
		}

		return $this->set( 'order.service.position', $value );
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$price = $this->getPrice();
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.service.siteid': !$private ?: $item->setSiteId( $value ); break;
				case 'order.service.parentid': !$private ?: $item->setParentId( $value ); break;
				case 'order.service.serviceid': !$private ?: $item->setServiceId( $value ); break;
				case 'order.service.type': $item->setType( $value ); break;
				case 'order.service.code': $item->setCode( $value ); break;
				case 'order.service.name': $item->setName( $value ); break;
				case 'order.service.currencyid': $price = $price->setCurrencyId( $value ); break;
				case 'order.service.price': $price = $price->setValue( $value ); break;
				case 'order.service.costs': $price = $price->setCosts( $value ); break;
				case 'order.service.rebate': $price = $price->setRebate( $value ); break;
				case 'order.service.taxrates': $price = $price->setTaxRates( $value ); break;
				case 'order.service.taxvalue': $price = $price->setTaxValue( $value ); break;
				case 'order.service.taxflag': $price = $price->setTaxFlag( $value ); break;
				case 'order.service.position': $item->setPosition( $value ); break;
				case 'order.service.mediaurl': $item->setMediaUrl( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values.
	 */
	public function toArray( bool $private = false ) : array
	{
		$price = $this->getPrice();
		$list = parent::toArray( $private );

		$list['order.service.type'] = $this->getType();
		$list['order.service.code'] = $this->getCode();
		$list['order.service.name'] = $this->getName();
		$list['order.service.currencyid'] = $price->getCurrencyId();
		$list['order.service.price'] = $price->getValue();
		$list['order.service.costs'] = $price->getCosts();
		$list['order.service.rebate'] = $price->getRebate();
		$list['order.service.taxrates'] = $price->getTaxRates();
		$list['order.service.taxvalue'] = $price->getTaxValue();
		$list['order.service.taxflag'] = $price->getTaxFlag();
		$list['order.service.position'] = $this->getPosition();
		$list['order.service.mediaurl'] = $this->getMediaUrl();
		$list['order.service.serviceid'] = $this->getServiceId();

		if( $private === true ) {
			$list['order.service.parentid'] = $this->getParentId();
		}

		return $list;
	}


	/**
	 * Copys all data from a given service item.
	 *
	 * @param \Aimeos\MShop\Service\Item\Iface $service New service item
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Service\Item\Iface $service ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		if( self::macro( 'copyFrom' ) ) {
			return $this->call( 'copyFrom', $service );
		}

		$values = $service->toArray();
		$this->fromArray( $values );

		$this->setSiteId( $service->getSiteId() );
		$this->setCode( $service->getCode() );
		$this->setName( $service->getName() );
		$this->setType( $service->getType() );
		$this->setServiceId( $service->getId() );

		if( ( $item = $service->getRefItems( 'media', 'default', 'default' )->first() ) !== null ) {
			$this->setMediaUrl( $item->getUrl() );
		}

		return $this->setModified();
	}
}
