<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Service\Transaction;


/**
 * Default order item base service transaction.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Order\Item\Service\Transaction\Iface
{
	use \Aimeos\MShop\Common\Item\Config\Traits;

	private \Aimeos\MShop\Price\Item\Iface $price;


	/**
	 * Initializes the order item base service transaction item.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price object
	 * @param array $values Associative array of key/value pairs
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, array $values = [] )
	{
		parent::__construct( 'order.service.transaction.', $values );
		$this->price = $price;
	}


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return string Site ID (or null if not available)
	 */
	public function getSiteId() : string
	{
		return $this->get( 'order.service.transaction.siteid', '' );
	}


	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service transaction item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Order\Item\Service\Transaction\Iface
	{
		return $this->set( 'order.service.transaction.siteid', $value );
	}


	/**
	 * Returns the ID of the ordered service item as parent
	 *
	 * @return string|null ID of the ordered service item
	 */
	public function getParentId() : ?string
	{
		return $this->get( 'order.service.transaction.parentid' );
	}


	/**
	 * Sets the ID of the ordered service item as parent
	 *
	 * @param string|null $id ID of the ordered service item
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service transaction item for chaining method calls
	 */
	public function setParentId( ?string $id ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.service.transaction.parentid', $id );
	}


	/**
	 * Returns the type of the service transaction item.
	 *
	 * @return string Type of the service transaction item
	 */
	public function getType() : string
	{
		return $this->get( 'order.service.transaction.type', '' );
	}


	/**
	 * Sets a new type for the service transaction item.
	 *
	 * @param string $type Type of the service transaction
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service transaction item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.service.transaction.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the configuration values of the item
	 *
	 * @return array Configuration values
	 */
	public function getConfig() : array
	{
		return $this->get( 'order.service.transaction.config', [] );
	}


	/**
	 * Sets the configuration values of the item.
	 *
	 * @param array $config Configuration values
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service transaction item for chaining method calls
	 */
	public function setConfig( array $config ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.service.transaction.config', $config );
	}


	/**
	 * Returns the price item for the transaction.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate
	 */
	public function getPrice() : \Aimeos\MShop\Price\Item\Iface
	{
		return $this->price;
	}


	/**
	 * Sets the price item for the transaction.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item containing price and additional costs
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service transaction item for chaining method calls
	 */
	public function setPrice( \Aimeos\MShop\Price\Item\Iface $price ) : \Aimeos\MShop\Order\Item\Service\Transaction\Iface
	{
		if( $price !== $this->price )
		{
			$this->price = $price;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the status of the transaction
	 *
	 * @return int Status of the transaction
	 */
	public function getStatus() : int
	{
		return $this->get( 'order.service.transaction.status', -1 );
	}


	/**
	 * Sets the new status of the transaction
	 *
	 * @param int $status New status of the transaction
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order base service transaction item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.service.transaction.status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'order/service/transaction';
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface Order service transaction item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );
		$price = $item->getPrice();

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.service.transaction.parentid': !$private ?: $item = $item->setParentId( $value ); break;
				case 'order.service.transaction.siteid': !$private ?: $item = $item->setSiteId( $value ); break;
				case 'order.service.transaction.config': $item = $item->setConfig( (array) $value ); break;
				case 'order.service.transaction.status': $item = $item->setStatus( (int) $value ); break;
				case 'order.service.transaction.currencyid': $price->setCurrencyId( $value ); break;
				case 'order.service.transaction.type': $item = $item->setType( $value ); break;
				case 'order.service.transaction.price': $price->setValue( $value ); break;
				case 'order.service.transaction.costs': $price->setCosts( $value ); break;
				case 'order.service.transaction.rebate': $price->setRebate( $value ); break;
				case 'order.service.transaction.taxvalue': $price->setTaxvalue( $value ); break;
				case 'order.service.transaction.taxflag': $price->setTaxflag( (bool) $value ); break;
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
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );
		$price = $this->getPrice();

		$list['order.service.transaction.type'] = $this->getType();
		$list['order.service.transaction.config'] = $this->getConfig();
		$list['order.service.transaction.status'] = $this->getStatus();
		$list['order.service.transaction.currencyid'] = $price->getCurrencyId();
		$list['order.service.transaction.price'] = $price->getValue();
		$list['order.service.transaction.costs'] = $price->getCosts();
		$list['order.service.transaction.rebate'] = $price->getRebate();
		$list['order.service.transaction.taxvalue'] = $price->getTaxvalue();
		$list['order.service.transaction.taxflag'] = $price->getTaxflag();

		if( $private === true ) {
			$list['order.service.transaction.parentid'] = $this->getParentId();
		}

		return $list;
	}
}
