<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
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


	/**
	 * Initializes the rule object
	 *
	 * @param array $values Associative array of id, type, name, config and status
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'rule.', $values );
	}


	/**
	 * Returns the type of the rule.
	 *
	 * @return string|null Rule type
	 */
	public function getType() : ?string
	{
		return $this->get( 'rule.type', 'catalog' );
	}


	/**
	 * Sets the new type of the rule item.
	 *
	 * @param string $type New rule type
	 * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'rule.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the provider of the rule.
	 *
	 * @return string Rule provider which is the short rule class name
	 */
	public function getProvider() : string
	{
		return $this->get( 'rule.provider', '' );
	}


	/**
	 * Sets the new provider of the rule item which is the short
	 * name of the rule class name.
	 *
	 * @param string $provider Rule provider, esp. short rule class name
	 * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
	 */
	public function setProvider( string $provider ) : \Aimeos\MShop\Rule\Item\Iface
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
		return $this->get( 'rule.label', '' );
	}


	/**
	 * Sets the new label of the rule item.
	 *
	 * @param string $label New label of the rule item
	 * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Rule\Item\Iface
	{
		return $this->set( 'rule.label', $label );
	}


	/**
	 * Returns the configuration of the rule item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig() : array
	{
		return $this->get( 'rule.config', [] );
	}


	/**
	 * Sets the new configuration for the rule item.
	 *
	 * @param array $config Custom configuration values
	 * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
	 */
	public function setConfig( array $config ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'rule.config', $config );
	}


	/**
	 * Returns the starting point of time, in which the rule is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart() : ?string
	{
		$value = $this->get( 'rule.datestart' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new starting point of time, in which the rule is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDateStart( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'rule.datestart', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the ending point of time, in which the rule is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd() : ?string
	{
		$value = $this->get( 'rule.dateend' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new ending point of time, in which the rule is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'rule.dateend', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the position of the rule item.
	 *
	 * @return int Position of the item
	 */
	public function getPosition() : int
	{
		return $this->get( 'rule.position', 0 );
	}


	/**
	 * Sets the new position of the rule item.
	 *
	 * @param int $position Position of the item
	 * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
	 */
	public function setPosition( int $position ) : \Aimeos\MShop\Common\Item\Iface
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
		return $this->get( 'rule.status', 1 );
	}


	/**
	 * Sets the new status of the rule item.
	 *
	 * @param int $status Status of the item
	 * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'rule.status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'rule';
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
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'rule.type': $item = $item->setType( $value ); break;
				case 'rule.label': $item = $item->setLabel( $value ); break;
				case 'rule.provider': $item = $item->setProvider( $value ); break;
				case 'rule.status': $item = $item->setStatus( (int) $value ); break;
				case 'rule.config': $item = $item->setConfig( (array) $value ); break;
				case 'rule.position': $item = $item->setPosition( (int) $value ); break;
				case 'rule.datestart': $item = $item->setDateStart( $value ); break;
				case 'rule.dateend': $item = $item->setDateEnd( $value ); break;
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
