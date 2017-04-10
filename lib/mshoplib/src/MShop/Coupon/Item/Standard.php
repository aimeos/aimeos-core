<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	private $values;

	/**
	 * Initializes the coupon item.
	 *
	 * @param array $values Optional; Associative array with id, label, provider,
	 * config and status to initialize the item properties
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'coupon.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the label of the coupon item.
	 *
	 * @return string Name/label for this coupon
	 */
	public function getLabel()
	{
		if( isset( $this->values['coupon.label'] ) ) {
			return (string) $this->values['coupon.label'];
		}

		return '';
	}


	/**
	 * Sets the label of the coupon item.
	 *
	 * @param string $name Coupon name, esp. short coupon class name
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setLabel( $name )
	{
		if( $name == $this->getLabel() ) { return $this; }

		$this->values['coupon.label'] = (string) $name;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the starting point of time, in which the coupon is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart()
	{
		if( isset( $this->values['coupon.datestart'] ) ) {
			return (string) $this->values['coupon.datestart'];
		}

		return null;
	}


	/**
	 * Sets a new starting point of time, in which the coupon is available.
	 *
	 * @param string New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setDateStart( $date )
	{
		if( $date == $this->getDateStart() ) { return $this; }

		$this->values['coupon.datestart'] = $this->checkDateFormat( $date );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the ending point of time, in which the coupon is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd()
	{
		if( isset( $this->values['coupon.dateend'] ) ) {
			return (string) $this->values['coupon.dateend'];
		}

		return null;
	}


	/**
	 * Sets a new ending point of time, in which the coupon is available.
	 *
	 * @param string New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setDateEnd( $date )
	{
		if( $date == $this->getDateEnd() ) { return $this; }

		$this->values['coupon.dateend'] = $this->checkDateFormat( $date );

		$this->setModified();

		return $this;
	}


	/**
	 * Returns the name of the provider class name to be used.
	 *
	 * @return string Returns the provider class name
	 */
	public function getProvider()
	{
		if( isset( $this->values['coupon.provider'] ) ) {
			return (string) $this->values['coupon.provider'];
		}

		return '';
	}


	/**
	 * Set the name of the provider class name to be used.
	 *
	 * @param string $provider Provider class name
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setProvider( $provider )
	{
		if( $provider == $this->getProvider() ) { return $this; }

		$this->values['coupon.provider'] = (string) $provider;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the configuration of the coupon item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig()
	{
		if( isset( $this->values['coupon.config'] ) && is_array( $this->values['coupon.config'] ) ) {
			return (array) $this->values['coupon.config'];
		}

		return [];
	}


	/**
	 * Sets the new configuration for the coupon item.
	 *
	 * @param array $config Custom configuration values
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setConfig( array $config )
	{
		if( $config == $this->getConfig() ) { return $this; }

		$this->values['coupon.config'] = $config;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the status of the coupon item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values['coupon.status'] ) ) {
			return (int) $this->values['coupon.status'];
		}

		return 0;
	}


	/**
	 * Sets the new status of the coupon item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return $this; }

		$this->values['coupon.status'] = (int) $status;
		$this->setModified();

		return $this;
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
				case 'coupon.config': $this->setConfig( $value ); break;
				case 'coupon.label': $this->setLabel( $value ); break;
				case 'coupon.datestart': $this->setDateStart( $value ); break;
				case 'coupon.dateend': $this->setDateEnd( $value ); break;
				case 'coupon.provider': $this->setProvider( $value ); break;
				case 'coupon.status': $this->setStatus( $value ); break;
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

		$list['coupon.config'] = $this->getConfig();
		$list['coupon.label'] = $this->getLabel();
		$list['coupon.datestart'] = $this->getDateStart();
		$list['coupon.dateend'] = $this->getDateEnd();
		$list['coupon.provider'] = $this->getProvider();
		$list['coupon.status'] = $this->getStatus();

		return $list;
	}

}
