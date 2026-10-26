<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2026
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Item;


/**
 * Default implementation of rule items.
 *
 * @package MShop
 * @subpackage Rule
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Rule\Item\Iface
{
	use \Aimeos\MShop\Common\Item\Config\Traits;
	use \Aimeos\MShop\Common\Item\TypeRef\Traits;


	/**
	 * Returns the type of the rule.
	 * Overwritten for different default value.
	 *
	 * @return string Rule type
	 */
	public function getType() : string
	{
		return (string) $this->get( 'rule.type', 'catalog' );
	}


	/**
	 * Returns the provider of the rule.
	 *
	 * @return string Rule provider which is the short rule class name
	 */
	public function getProvider() : string
	{
		return (string) $this->get( 'rule.provider', '' );
	}


	/**
	 * Sets the new provider of the rule item which is the short
	 * name of the rule class name.
	 *
	 * @param string $provider Rule provider, esp. short rule class name
	 * @return static Rule item for chaining method calls
	 */
	public function setProvider( string $provider ) : static
	{
		if( preg_match( '/^[A-Za-z0-9]+(,[A-Za-z0-9]+)*$/', $provider ) !== 1 ) {
			throw new \Aimeos\MShop\Rule\Exception( sprintf( 'Invalid provider name "%1$s"', $provider ) );
		}

		return $this->set( 'rule.provider', $provider );
	}


	/**
	 * Returns the name of the rule item.
	 *
	 * @return string Label of the rule item
	 */
	public function getLabel() : string
	{
		return (string) $this->get( 'rule.label', '' );
	}


	/**
	 * Sets the new label of the rule item.
	 *
	 * @param string $label New label of the rule item
	 * @return static Rule item for chaining method calls
	 */
	public function setLabel( string $label ) : static
	{
		return $this->set( 'rule.label', $label );
	}


	/**
	 * Returns the starting point of time, in which the rule is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart() : ?string
	{
		$value = $this->get( 'rule.datestart' );
		return $value ? substr( (string) $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new starting point of time, in which the rule is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return static Product item for chaining method calls
	 */
	public function setDateStart( ?string $date ) : static
	{
		return $this->set( 'rule.datestart', \Aimeos\Utils::datetime( $date ) );
	}


	/**
	 * Returns the ending point of time, in which the rule is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd() : ?string
	{
		$value = $this->get( 'rule.dateend' );
		return $value ? substr( (string) $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new ending point of time, in which the rule is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return static Product item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : static
	{
		return $this->set( 'rule.dateend', \Aimeos\Utils::datetime( $date ) );
	}


	/**
	 * Returns the position of the rule item.
	 *
	 * @return int Position of the item
	 */
	public function getPosition() : int
	{
		return (int) $this->get( 'rule.position', 0 );
	}


	/**
	 * Sets the new position of the rule item.
	 *
	 * @param int $position Position of the item
	 * @return static Rule item for chaining method calls
	 */
	public function setPosition( int $position ) : static
	{
		return $this->set( 'rule.position', $position );
	}


	/**
	 * Returns the status of the rule item.
	 *
	 * @return int Status of the item
	 */
	public function getStatus() : int
	{
		return (int) $this->get( 'rule.status', 1 );
	}


	/**
	 * Sets the new status of the rule item.
	 *
	 * @param int $status Status of the item
	 * @return static Rule item for chaining method calls
	 */
	public function setStatus( int $status ) : static
	{
		return $this->set( 'rule.status', $status );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Rule item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'rule.type': $item->setType( (string) $value ); break;
				case 'rule.label': $item->setLabel( (string) $value ); break;
				case 'rule.provider': $item->setProvider( (string) $value ); break;
				case 'rule.status': $item->setStatus( (int) $value ); break;
				case 'rule.config': $item->setConfig( (array) $value ); break;
				case 'rule.position': $item->setPosition( (int) $value ); break;
				case 'rule.datestart': $item->setDateStart( $value ? (string) $value : null ); break;
				case 'rule.dateend': $item->setDateEnd( $value ? (string) $value : null ); break;
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

		$list['rule.type'] = $this->getType();
		$list['rule.label'] = $this->getLabel();
		$list['rule.provider'] = $this->getProvider();
		$list['rule.status'] = $this->getStatus();
		$list['rule.config'] = $this->getConfig();
		$list['rule.position'] = $this->getPosition();
		$list['rule.datestart'] = $this->getDateStart();
		$list['rule.dateend'] = $this->getDateEnd();

		return $list;
	}

}
