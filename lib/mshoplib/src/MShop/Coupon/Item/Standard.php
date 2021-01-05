<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Item;


/**
 * Default coupon item implementation.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Coupon\Item\Iface
{
	use \Aimeos\MShop\Common\Item\Config\Traits;


	private $date;


	/**
	 * Initializes the coupon item.
	 *
	 * @param array $values Optional; Associative array with id, label, provider,
	 * config and status to initialize the item properties
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'coupon.', $values );

		$this->date = isset( $values['.date'] ) ? $values['.date'] : date( 'Y-m-d H:i:s' );
	}


	/**
	 * Returns the label of the coupon item.
	 *
	 * @return string Name/label for this coupon
	 */
	public function getLabel() : string
	{
		return $this->get( 'coupon.label', '' );
	}


	/**
	 * Sets the label of the coupon item.
	 *
	 * @param string $name Coupon name, esp. short coupon class name
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setLabel( string $name ) : \Aimeos\MShop\Coupon\Item\Iface
	{
		return $this->set( 'coupon.label', $name );
	}


	/**
	 * Returns the starting point of time, in which the coupon is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart() : ?string
	{
		$value = $this->get( 'coupon.datestart' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new starting point of time, in which the coupon is available.
	 *
	 * @param string $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setDateStart( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'coupon.datestart', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the ending point of time, in which the coupon is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd() : ?string
	{
		$value = $this->get( 'coupon.dateend' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new ending point of time, in which the coupon is available.
	 *
	 * @param string $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'coupon.dateend', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the name of the provider class name to be used.
	 *
	 * @return string Returns the provider class name
	 */
	public function getProvider() : string
	{
		return $this->get( 'coupon.provider', '' );
	}


	/**
	 * Set the name of the provider class name to be used.
	 *
	 * @param string $provider Provider class name
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setProvider( string $provider ) : \Aimeos\MShop\Coupon\Item\Iface
	{
		return $this->set( 'coupon.provider', $provider );
	}


	/**
	 * Returns the configuration of the coupon item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig() : array
	{
		return $this->get( 'coupon.config', [] );
	}


	/**
	 * Sets the new configuration for the coupon item.
	 *
	 * @param array $config Custom configuration values
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setConfig( array $config ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'coupon.config', $config );
	}


	/**
	 * Returns the status of the coupon item.
	 *
	 * @return int Status of the item
	 */
	public function getStatus() : int
	{
		return $this->get( 'coupon.status', 1 );
	}


	/**
	 * Sets the new status of the coupon item.
	 *
	 * @param int $status Status of the item
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'coupon.status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'coupon';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0
			&& ( $this->getDateStart() === null || $this->getDateStart() < $this->date )
			&& ( $this->getDateEnd() === null || $this->getDateEnd() > $this->date );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'coupon.label': $item = $item->setLabel( $value ); break;
				case 'coupon.datestart': $item = $item->setDateStart( $value ); break;
				case 'coupon.dateend': $item = $item->setDateEnd( $value ); break;
				case 'coupon.provider': $item = $item->setProvider( $value ); break;
				case 'coupon.status': $item = $item->setStatus( (int) $value ); break;
				case 'coupon.config': $item = $item->setConfig( (array) $value ); break;
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

		$list['coupon.config'] = $this->getConfig();
		$list['coupon.label'] = $this->getLabel();
		$list['coupon.datestart'] = $this->getDateStart();
		$list['coupon.dateend'] = $this->getDateEnd();
		$list['coupon.provider'] = $this->getProvider();
		$list['coupon.status'] = $this->getStatus();

		return $list;
	}

}
