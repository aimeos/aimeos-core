<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Default coupon item implementation.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Item_Standard
	extends MShop_Common_Item_Base
	implements MShop_Coupon_Item_Iface
{
	private $values;

	/**
	 * Initializes the coupon item.
	 *
	 * @param array $values Optional; Associative array with id, label, provider,
	 * config and status to initialize the item properties
	 */
	public function __construct( array $values = array( ) )
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
		return ( isset( $this->values['label'] ) ? (string) $this->values['label'] : '' );
	}


	/**
	 * Sets the label of the coupon item.
	 *
	 * @param string $name Coupon name, esp. short coupon class name
	 */
	public function setLabel( $name )
	{
		if( $name == $this->getLabel() ) { return; }

		$this->values['label'] = (string) $name;
		$this->setModified();
	}


	/**
	 * Returns the starting point of time, in which the coupon is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart()
	{
		return ( isset( $this->values['start'] ) ? (string) $this->values['start'] : null );
	}


	/**
	 * Sets a new starting point of time, in which the coupon is available.
	 *
	 * @param string New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateStart( $date )
	{
		if( $date == $this->getDateStart() ) { return; }

		$this->checkDateFormat( $date );

		$this->values['start'] = ( $date !== null ? (string) $date : null );

		$this->setModified();
	}


	/**
	 * Returns the ending point of time, in which the coupon is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd()
	{
		return ( isset( $this->values['end'] ) ? (string) $this->values['end'] : null );
	}


	/**
	 * Sets a new ending point of time, in which the coupon is available.
	 *
	 * @param string New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateEnd( $date )
	{
		if( $date == $this->getDateEnd() ) { return; }

		$this->checkDateFormat( $date );

		$this->values['end'] = ( $date !== null ? (string) $date : null );

		$this->setModified();
	}


	/**
	 * Returns the name of the provider class to be used.
	 *
	 * @return string Returns the methode name to use
	 */
	public function getProvider()
	{
		return ( isset( $this->values['provider'] ) ? (string) $this->values['provider'] : '' );
	}


	/**
	 * Set the name of the provider class to be used.
	 *
	 * @param string $provider
	 */
	public function setProvider( $provider )
	{
		if( $provider == $this->getProvider() ) { return; }

		$this->values['provider'] = (string) $provider;
		$this->setModified();
	}


	/**
	 * Returns the configuration of the coupon item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig()
	{
		return ( isset( $this->values['config'] ) && is_array( $this->values['config'] ) ? (array) $this->values['config'] : array() );
	}


	/**
	 * Sets the new configuration for the coupon item.
	 *
	 * @param array $config Custom configuration values
	 */
	public function setConfig( array $config )
	{
		if( $config == $this->getConfig() ) { return; }

		$this->values['config'] = $config;
		$this->setModified();
	}


	/**
	 * Returns the status of the coupon item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->values['status'] ) ? (int) $this->values['status'] : 0 );
	}


	/**
	 * Sets the new status of the coupon item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return; }

		$this->values['status'] = (int) $status;
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
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['coupon.config'] = $this->getConfig();
		$list['coupon.label'] = $this->getLabel();
		$list['coupon.datestart'] = $this->getDateStart();
		$list['coupon.dateend'] = $this->getDateEnd();
		$list['coupon.provider'] = $this->getProvider();
		$list['coupon.status'] = $this->getStatus();

		return $list;
	}

}
