<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	private $values;

	/**
	 * Initializes the plugin object
	 *
	 * @param array $values Associative array of id, typeid, name, config and status
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'plugin.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the type of the plugin.
	 *
	 * @return string|null Plugin type
	 */
	public function getType()
	{
		if( isset( $this->values['plugin.type'] ) ) {
			return (string) $this->values['plugin.type'];
		}

		return null;
	}


	/**
	 * Returns the localized name of the type
	 *
	 * @return string|null Localized name of the type
	 */
	public function getTypeName()
	{
		if( isset( $this->values['plugin.typename'] ) ) {
			return (string) $this->values['plugin.typename'];
		}

		return null;
	}


	/**
	 * Returns the type ID of the plugin.
	 *
	 * @return integer|null Plugin type ID
	 */
	public function getTypeId()
	{
		if( isset( $this->values['plugin.typeid'] ) ) {
			return (int) $this->values['plugin.typeid'];
		}

		return null;
	}


	/**
	 * Sets the new type ID of the plugin item.
	 *
	 * @param integer $typeid New plugin type ID
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setTypeId( $typeid )
	{
		if( $typeid == $this->getTypeId() ) { return $this; }

		$this->values['plugin.typeid'] = (int) $typeid;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the provider of the plugin.
	 *
	 * @return string Plugin provider which is the short plugin class name
	 */
	public function getProvider()
	{
		if( isset( $this->values['plugin.provider'] ) ) {
			return (string) $this->values['plugin.provider'];
		}

		return '';
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
		if( $provider == $this->getProvider() ) { return $this; }

		$this->values['plugin.provider'] = (string) $provider;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the name of the plugin item.
	 *
	 * @return string Label of the plugin item
	 */
	public function getLabel()
	{
		if( isset( $this->values['plugin.label'] ) ) {
			return (string) $this->values['plugin.label'];
		}

		return '';
	}


	/**
	 * Sets the new label of the plugin item.
	 *
	 * @param string $label New label of the plugin item
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return $this; }

		$this->values['plugin.label'] = (string) $label;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the configuration of the plugin item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig()
	{
		if( isset( $this->values['plugin.config'] ) ) {
			return (array) $this->values['plugin.config'];
		}

		return [];
	}


	/**
	 * Sets the new configuration for the plugin item.
	 *
	 * @param array $config Custom configuration values
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setConfig( array $config )
	{
		$this->values['plugin.config'] = $config;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the position of the plugin item.
	 *
	 * @return integer Position of the item
	 */
	public function getPosition()
	{
		if( isset( $this->values['plugin.position'] ) ) {
			return (int) $this->values['plugin.position'];
		}

		return 0;
	}


	/**
	 * Sets the new position of the plugin item.
	 *
	 * @param integer $position Position of the item
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setPosition( $position )
	{
		if( $position == $this->getPosition() ) { return $this; }

		$this->values['plugin.position'] = (int) $position;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the status of the plugin item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values['plugin.status'] ) ) {
			return (int) $this->values['plugin.status'];
		}

		return 0;
	}


	/**
	 * Sets the new status of the plugin item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return $this; }

		$this->values['plugin.status'] = (int) $status;
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
		return 'plugin';
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
		unset( $list['plugin.type'], $list['plugin.typename'] );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'plugin.typeid': $this->setTypeId( $value ); break;
				case 'plugin.label': $this->setLabel( $value ); break;
				case 'plugin.provider': $this->setProvider( $value ); break;
				case 'plugin.config': $this->setConfig( $value ); break;
				case 'plugin.status': $this->setStatus( $value ); break;
				case 'plugin.position': $this->setPosition( $value ); break;
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

		$list['plugin.type'] = $this->getType();
		$list['plugin.typename'] = $this->getTypeName();
		$list['plugin.label'] = $this->getLabel();
		$list['plugin.provider'] = $this->getProvider();
		$list['plugin.config'] = $this->getConfig();
		$list['plugin.status'] = $this->getStatus();
		$list['plugin.position'] = $this->getPosition();

		if( $private === true ) {
			$list['plugin.typeid'] = $this->getTypeId();
		}

		return $list;
	}

}
