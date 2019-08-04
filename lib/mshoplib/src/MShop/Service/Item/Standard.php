<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Item;


/**
 * Service item with common methods.
 *
 * @package MShop
 * @subpackage Service
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Service\Item\Iface
{
	use \Aimeos\MShop\Common\Item\Config\Traits;
	use \Aimeos\MShop\Common\Item\ListRef\Traits;


	private $values;


	/**
	 * Initializes the item object.
	 *
	 * @param array $values Parameter for initializing the basic properties
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = [], array $listItems = [], array $refItems = [] )
	{
		parent::__construct( 'service.', $values );

		$this->initListItems( $listItems, $refItems );
		$this->values = $values;
	}


	/**
	 * Returns the code of the service item if available
	 *
	 * @return string Service item code
	 */
	public function getCode()
	{
		if( isset( $this->values['service.code'] ) ) {
			return (string) $this->values['service.code'];
		}

		return '';
	}


	/**
	 * Sets the code of the service item
	 *
	 * @param string $code Code of the service item
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setCode( $code )
	{
		if( (string) $code !== $this->getCode() )
		{
			$this->values['service.code'] = $this->checkCode( $code );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the type of the service item if available.
	 *
	 * @return string|null Service item type
	 */
	public function getType()
	{
		if( isset( $this->values['service.type'] ) ) {
			return (string) $this->values['service.type'];
		}
	}


	/**
	 * Sets the type of the service item.
	 *
	 * @param string $type Type of the service item
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setType( $type )
	{
		if( (string) $type !== $this->getType() )
		{
			$this->values['service.type'] = $this->checkCode( $type );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the name of the service provider the item belongs to.
	 *
	 * @return string Name of the service provider
	 */
	public function getProvider()
	{
		if( isset( $this->values['service.provider'] ) ) {
			return (string) $this->values['service.provider'];
		}

		return '';
	}


	/**
	 * Sets the new name of the service provider the item belongs to.
	 *
	 * @param string $provider Name of the service provider
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setProvider( $provider )
	{
		if( (string) $provider !== $this->getProvider() )
		{
			if( preg_match( '/^[A-Za-z0-9]+(,[A-Za-z0-9]+)*$/', $provider ) !== 1 ) {
				throw new \Aimeos\MShop\Service\Exception( sprintf( 'Invalid provider name "%1$s"', $provider ) );
			}

			$this->values['service.provider'] = (string) $provider;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the label of the service item if available.
	 *
	 * @return string Service item label
	 */
	public function getLabel()
	{
		if( isset( $this->values['service.label'] ) ) {
			return (string) $this->values['service.label'];
		}

		return '';
	}


	/**
	 * Sets the label of the service item
	 *
	 * @param string $label Label of the service item
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( (string) $label !== $this->getLabel() )
		{
			$this->values['service.label'] = (string) $label;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the starting point of time, in which the service is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart()
	{
		if( isset( $this->values['service.datestart'] ) ) {
			return (string) $this->values['service.datestart'];
		}
	}


	/**
	 * Sets a new starting point of time, in which the service is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDateStart( $date )
	{
		if( $date !== $this->getDateStart() )
		{
			$this->values['service.datestart'] = $this->checkDateFormat( $date );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the ending point of time, in which the service is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd()
	{
		if( isset( $this->values['service.dateend'] ) ) {
			return (string) $this->values['service.dateend'];
		}
	}


	/**
	 * Sets a new ending point of time, in which the service is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDateEnd( $date )
	{
		if( $date !== $this->getDateEnd() )
		{
			$this->values['service.dateend'] = $this->checkDateFormat( $date );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the configuration values of the item
	 *
	 * @return array Configuration values
	 */
	public function getConfig()
	{
		if( isset( $this->values['service.config'] ) ) {
			return (array) $this->values['service.config'];
		}

		return [];
	}


	/**
	 * Sets the configuration values of the item.
	 *
	 * @param array $config Configuration values
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setConfig( array $config )
	{
		$this->values['service.config'] = $config;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the position of the service item in the list of deliveries.
	 *
	 * @return integer Position in item list
	 */
	public function getPosition()
	{
		if( isset( $this->values['service.position'] ) ) {
			return (int) $this->values['service.position'];
		}

		return 0;
	}


	/**
	 * Sets the new position of the service item in the list of deliveries.
	 *
	 * @param integer $pos Position in item list
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setPosition( $pos )
	{
		if( (int) $pos !== $this->getPosition() )
		{
			$this->values['service.position'] = (int) $pos;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values['service.status'] ) ) {
			return (int) $this->values['service.status'];
		}

		return 1;
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( (int) $status !== $this->getStatus() )
		{
			$this->values['service.status'] = (int) $status;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'service';
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
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'service.type': $item = $item->setType( $value ); break;
				case 'service.code': $item = $item->setCode( $value ); break;
				case 'service.label': $item = $item->setLabel( $value ); break;
				case 'service.provider': $item = $item->setProvider( $value ); break;
				case 'service.position': $item = $item->setPosition( $value ); break;
				case 'service.datestart': $item = $item->setDateStart( $value ); break;
				case 'service.dateend': $item = $item->setDateEnd( $value ); break;
				case 'service.config': $item = $item->setConfig( $value ); break;
				case 'service.status': $item = $item->setStatus( $value ); break;
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

		$list['service.type'] = $this->getType();
		$list['service.code'] = $this->getCode();
		$list['service.label'] = $this->getLabel();
		$list['service.provider'] = $this->getProvider();
		$list['service.position'] = $this->getPosition();
		$list['service.datestart'] = $this->getDateStart();
		$list['service.dateend'] = $this->getDateEnd();
		$list['service.config'] = $this->getConfig();
		$list['service.status'] = $this->getStatus();

		return $list;
	}

}
