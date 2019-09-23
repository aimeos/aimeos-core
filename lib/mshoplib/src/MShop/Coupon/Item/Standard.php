<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
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


	/**
	 * Initializes the coupon item.
	 *
	 * @param array $values Optional; Associative array with id, label, provider,
	 * config and status to initialize the item properties
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'coupon.', $values );
	}


	/**
	 * Returns the label of the coupon item.
	 *
	 * @return string Name/label for this coupon
	 */
	public function getLabel()
	{
		return (string) $this->get( 'coupon.label', '' );
	}


	/**
	 * Sets the label of the coupon item.
	 *
	 * @param string $name Coupon name, esp. short coupon class name
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setLabel( $name )
	{
		return $this->set( 'coupon.label', (string) $name );
	}


	/**
	 * Returns the starting point of time, in which the coupon is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart()
	{
		return $this->get( 'coupon.datestart' );
	}


	/**
	 * Sets a new starting point of time, in which the coupon is available.
	 *
	 * @param string $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setDateStart( $date )
	{
		return $this->set( 'coupon.datestart', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the ending point of time, in which the coupon is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd()
	{
		return $this->get( 'coupon.dateend' );
	}


	/**
	 * Sets a new ending point of time, in which the coupon is available.
	 *
	 * @param string $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setDateEnd( $date )
	{
		return $this->set( 'coupon.dateend', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the name of the provider class name to be used.
	 *
	 * @return string Returns the provider class name
	 */
	public function getProvider()
	{
		return (string) $this->get( 'coupon.provider', '' );
	}


	/**
	 * Set the name of the provider class name to be used.
	 *
	 * @param string $provider Provider class name
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setProvider( $provider )
	{
		return $this->set( 'coupon.provider', (string) $provider );
	}


	/**
	 * Returns the configuration of the coupon item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig()
	{
		return (array) $this->get( 'coupon.config', [] );
	}


	/**
	 * Sets the new configuration for the coupon item.
	 *
	 * @param array $config Custom configuration values
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setConfig( array $config )
	{
		return $this->set( 'coupon.config', $config );
	}


	/**
	 * Returns the status of the coupon item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return (int) $this->get( 'coupon.status', 1 );
	}


	/**
	 * Sets the new status of the coupon item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setStatus( $status )
	{
		return $this->set( 'coupon.status', (int) $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'coupon';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return parent::isAvailable() && $this->getStatus() > 0
			&& ( $this->getDateStart() === null || $this->getDateStart() < $this->values['.date'] )
			&& ( $this->getDateEnd() === null || $this->getDateEnd() > $this->values['.date'] );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'coupon.config': $item = $item->setConfig( $value ); break;
				case 'coupon.label': $item = $item->setLabel( $value ); break;
				case 'coupon.datestart': $item = $item->setDateStart( $value ); break;
				case 'coupon.dateend': $item = $item->setDateEnd( $value ); break;
				case 'coupon.provider': $item = $item->setProvider( $value ); break;
				case 'coupon.status': $item = $item->setStatus( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
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

		$list['coupon.config'] = $this->getConfig();
		$list['coupon.label'] = $this->getLabel();
		$list['coupon.datestart'] = $this->getDateStart();
		$list['coupon.dateend'] = $this->getDateEnd();
		$list['coupon.provider'] = $this->getProvider();
		$list['coupon.status'] = $this->getStatus();

		return $list;
	}

}
