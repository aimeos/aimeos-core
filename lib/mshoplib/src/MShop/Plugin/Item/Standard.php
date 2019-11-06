<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Item;


/**
 * Default implementation of plugin items.
 *
 * @package MShop
 * @subpackage Plugin
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Plugin\Item\Iface
{
	use \Aimeos\MShop\Common\Item\Config\Traits;


	/**
	 * Initializes the plugin object
	 *
	 * @param array $values Associative array of id, type, name, config and status
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'plugin.', $values );
	}


	/**
	 * Returns the type of the plugin.
	 *
	 * @return string|null Plugin type
	 */
	public function getType()
	{
		return $this->get( 'plugin.type', 'order' );
	}


	/**
	 * Sets the new type of the plugin item.
	 *
	 * @param string $type New plugin type
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setType( $type )
	{
		return $this->set( 'plugin.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the provider of the plugin.
	 *
	 * @return string Plugin provider which is the short plugin class name
	 */
	public function getProvider()
	{
		return (string) $this->get( 'plugin.provider', '' );
	}


	/**
	 * Sets the new provider of the plugin item which is the short
	 * name of the plugin class name.
	 *
	 * @param string $provider Plugin provider, esp. short plugin class name
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setProvider( $provider )
	{
		if( preg_match( '/^[A-Za-z0-9]+(,[A-Za-z0-9]+)*$/', $provider ) !== 1 ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Invalid provider name "%1$s"', $provider ) );
		}

		return $this->set( 'plugin.provider', (string) $provider );
	}


	/**
	 * Returns the name of the plugin item.
	 *
	 * @return string Label of the plugin item
	 */
	public function getLabel()
	{
		return (string) $this->get( 'plugin.label', '' );
	}


	/**
	 * Sets the new label of the plugin item.
	 *
	 * @param string $label New label of the plugin item
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setLabel( $label )
	{
		return $this->set( 'plugin.label', (string) $label );
	}


	/**
	 * Returns the configuration of the plugin item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig()
	{
		return (array) $this->get( 'plugin.config', [] );
	}


	/**
	 * Sets the new configuration for the plugin item.
	 *
	 * @param array $config Custom configuration values
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setConfig( array $config )
	{
		return $this->set( 'plugin.config', $config );
	}


	/**
	 * Returns the position of the plugin item.
	 *
	 * @return integer Position of the item
	 */
	public function getPosition()
	{
		return (int) $this->get( 'plugin.position', 0 );
	}


	/**
	 * Sets the new position of the plugin item.
	 *
	 * @param integer $position Position of the item
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setPosition( $position )
	{
		return $this->set( 'plugin.position', (int) $position );
	}


	/**
	 * Returns the status of the plugin item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return (int) $this->get( 'plugin.status', 1 );
	}


	/**
	 * Sets the new status of the plugin item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setStatus( $status )
	{
		return $this->set( 'plugin.status', (int) $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'plugin';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'plugin.type': $item = $item->setType( $value ); break;
				case 'plugin.label': $item = $item->setLabel( $value ); break;
				case 'plugin.provider': $item = $item->setProvider( $value ); break;
				case 'plugin.config': $item = $item->setConfig( $value ); break;
				case 'plugin.status': $item = $item->setStatus( $value ); break;
				case 'plugin.position': $item = $item->setPosition( $value ); break;
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

		$list['plugin.type'] = $this->getType();
		$list['plugin.label'] = $this->getLabel();
		$list['plugin.provider'] = $this->getProvider();
		$list['plugin.config'] = $this->getConfig();
		$list['plugin.status'] = $this->getStatus();
		$list['plugin.position'] = $this->getPosition();

		return $list;
	}

}
