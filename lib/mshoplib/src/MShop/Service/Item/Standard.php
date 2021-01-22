<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	use \Aimeos\MShop\Common\Item\ListsRef\Traits;


	private $date;


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

		$this->date = ( isset( $values['.date'] ) ? $values['.date'] : null );
		$this->initListItems( $listItems, $refItems );
	}


	/**
	 * Returns the code of the service item if available
	 *
	 * @return string Service item code
	 */
	public function getCode() : string
	{
		return $this->get( 'service.code', '' );
	}


	/**
	 * Sets the code of the service item
	 *
	 * @param string $code Code of the service item
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Service\Item\Iface
	{
		return $this->set( 'service.code', $this->checkCode( $code ) );
	}


	/**
	 * Returns the type of the service item if available.
	 *
	 * @return string|null Service item type
	 */
	public function getType() : ?string
	{
		return $this->get( 'service.type' );
	}


	/**
	 * Sets the type of the service item.
	 *
	 * @param string $type Type of the service item
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'service.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the name of the service provider the item belongs to.
	 *
	 * @return string Name of the service provider
	 */
	public function getProvider() : string
	{
		return $this->get( 'service.provider', '' );
	}


	/**
	 * Sets the new name of the service provider the item belongs to.
	 *
	 * @param string $provider Name of the service provider
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setProvider( string $provider ) : \Aimeos\MShop\Service\Item\Iface
	{
		if( preg_match( '/^[A-Za-z0-9]+(,[A-Za-z0-9]+)*$/', $provider ) !== 1 ) {
			throw new \Aimeos\MShop\Service\Exception( sprintf( 'Invalid provider name "%1$s"', $provider ) );
		}

		return $this->set( 'service.provider', $provider );
	}


	/**
	 * Returns the label of the service item if available.
	 *
	 * @return string Service item label
	 */
	public function getLabel() : string
	{
		return $this->get( 'service.label', '' );
	}


	/**
	 * Sets the label of the service item
	 *
	 * @param string $label Label of the service item
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Service\Item\Iface
	{
		return $this->set( 'service.label', $label );
	}


	/**
	 * Returns the starting point of time, in which the service is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart() : ?string
	{
		$value = $this->get( 'service.datestart' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new starting point of time, in which the service is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDateStart( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'service.datestart', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the ending point of time, in which the service is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd() : ?string
	{
		$value = $this->get( 'service.dateend' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new ending point of time, in which the service is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'service.dateend', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the configuration values of the item
	 *
	 * @return array Configuration values
	 */
	public function getConfig() : array
	{
		return $this->get( 'service.config', [] );
	}


	/**
	 * Sets the configuration values of the item.
	 *
	 * @param array $config Configuration values
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setConfig( array $config ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'service.config', $config );
	}


	/**
	 * Returns the position of the service item in the list of deliveries.
	 *
	 * @return int Position in item list
	 */
	public function getPosition() : int
	{
		return $this->get( 'service.position', 0 );
	}


	/**
	 * Sets the new position of the service item in the list of deliveries.
	 *
	 * @param int $pos Position in item list
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setPosition( int $pos ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'service.position', $pos );
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return int Status of the item
	 */
	public function getStatus() : int
	{
		return $this->get( 'service.status', 1 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param int $status Status of the item
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'service.status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'service';
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


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
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
				case 'service.datestart': $item = $item->setDateStart( $value ); break;
				case 'service.dateend': $item = $item->setDateEnd( $value ); break;
				case 'service.status': $item = $item->setStatus( (int) $value ); break;
				case 'service.config': $item = $item->setConfig( (array) $value ); break;
				case 'service.position': $item = $item->setPosition( (int) $value ); break;
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
