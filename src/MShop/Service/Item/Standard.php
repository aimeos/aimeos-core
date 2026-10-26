<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
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
	use \Aimeos\MShop\Common\Item\TypeRef\Traits;


	/**
	 * Initializes the item object.
	 *
	 * @param string $prefix Domain specific prefix string
	 * @param array $values Parameter for initializing the basic properties
	 */
	public function __construct( string $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values );

		$this->initListItems( (array) ( $values['.listitems'] ?? [] ) );
	}


	/**
	 * Returns the code of the service item if available
	 *
	 * @return string Service item code
	 */
	public function getCode() : string
	{
		return (string) $this->get( 'service.code', '' );
	}


	/**
	 * Sets the code of the service item
	 *
	 * @param string $code Code of the service item
	 * @return static Service item for chaining method calls
	 */
	public function setCode( string $code ) : static
	{
		return $this->set( 'service.code', \Aimeos\Utils::code( $code ) );
	}


	/**
	 * Returns the name of the service provider the item belongs to.
	 *
	 * @return string Name of the service provider
	 */
	public function getProvider() : string
	{
		return (string) $this->get( 'service.provider', '' );
	}


	/**
	 * Sets the new name of the service provider the item belongs to.
	 *
	 * @param string $provider Name of the service provider
	 * @return static Service item for chaining method calls
	 */
	public function setProvider( string $provider ) : static
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
		return (string) $this->get( 'service.label', '' );
	}


	/**
	 * Sets the label of the service item
	 *
	 * @param string $label Label of the service item
	 * @return static Service item for chaining method calls
	 */
	public function setLabel( string $label ) : static
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
		return $value ? substr( (string) $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new starting point of time, in which the service is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return static Product item for chaining method calls
	 */
	public function setDateStart( ?string $date ) : static
	{
		return $this->set( 'service.datestart', \Aimeos\Utils::datetime( $date ) );
	}


	/**
	 * Returns the ending point of time, in which the service is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd() : ?string
	{
		$value = $this->get( 'service.dateend' );
		return $value ? substr( (string) $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new ending point of time, in which the service is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return static Product item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : static
	{
		return $this->set( 'service.dateend', \Aimeos\Utils::datetime( $date ) );
	}


	/**
	 * Returns the position of the service item in the list of deliveries.
	 *
	 * @return int Position in item list
	 */
	public function getPosition() : int
	{
		return (int) $this->get( 'service.position', 0 );
	}


	/**
	 * Sets the new position of the service item in the list of deliveries.
	 *
	 * @param int $pos Position in item list
	 * @return static Service item for chaining method calls
	 */
	public function setPosition( int $pos ) : static
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
		return (int) $this->get( 'service.status', 1 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param int $status Status of the item
	 * @return static Service item for chaining method calls
	 */
	public function setStatus( int $status ) : static
	{
		return $this->set( 'service.status', $status );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		$date = $this->get( '.date' ) ?: date( 'Y-m-d H:i:00' );

		return parent::isAvailable() && $this->getStatus() > 0
			&& ( $this->getDateStart() === null || $this->getDateStart() < $date )
			&& ( $this->getDateEnd() === null || $this->getDateEnd() > $date );
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Service item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'service.type': $item->setType( (string) $value ); break;
				case 'service.code': $item->setCode( (string) $value ); break;
				case 'service.label': $item->setLabel( (string) $value ); break;
				case 'service.provider': $item->setProvider( (string) $value ); break;
				case 'service.datestart': $item->setDateStart( $value ? (string) $value : null ); break;
				case 'service.dateend': $item->setDateEnd( $value ? (string) $value : null ); break;
				case 'service.status': $item->setStatus( (int) $value ); break;
				case 'service.config': $item->setConfig( (array) $value ); break;
				case 'service.position': $item->setPosition( (int) $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool $private True to return private properties, false for public only
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
