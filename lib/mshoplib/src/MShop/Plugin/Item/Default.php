<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Default implementation of plugin items.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Item_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Plugin_Item_Interface
{
	private $_values;

	/**
	 * Initializes the plugin object
	 *
	 * @param array $values Associative array of id, typeid, name, config and status
	 */
	public function __construct( array $values = array() )
	{
		parent::__construct( 'plugin.', $values) ;

		$this->_values = $values;
	}


	/**
	 * Returns the type of the plugin.
	 *
	 * @return string Plugin type
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : '' );
	}


	/**
	 * Returns the type ID of the plugin.
	 *
	 * @return integer Plugin type ID
	 */
	public function getTypeId()
	{
		return ( isset( $this->_values['typeid'] ) ? (int) $this->_values['typeid'] : null );
	}


	/**
	 * Sets the new type ID of the plugin item.
	 *
	 * @param integer $typeid New plugin type ID
	 */
	public function setTypeId( $typeid )
	{
		if ( $typeid == $this->getTypeId() ) { return; }

		$this->_values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the provider of the plugin.
	 *
	 * @return string Plugin provider which is the short plugin class name
	 */
	public function getProvider()
	{
		return ( isset( $this->_values['provider'] ) ? (string) $this->_values['provider'] : '' );
	}


	/**
	 * Returns the name of the plugin item.
	 *
	 * @return string Label of the plugin item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the new label of the plugin item.
	 *
	 * @param string $label New label of the plugin item
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) {
			return;
		}

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Sets the new provider of the plugin item which is the short
	 * name of the plugin class name.
	 *
	 * @param string $provider Plugin provider, esp. short plugin class name
	 */
	public function setProvider( $provider )
	{
		if ( $provider == $this->getProvider() ) { return; }

		$this->_values['provider'] = (string) $provider;
		$this->setModified();
	}


	/**
	 * Returns the configuration of the plugin item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig()
	{
		return ( isset( $this->_values['config'] ) ? (array) $this->_values['config'] : array() );
	}


	/**
	 * Sets the new configuration for the plugin item.
	 *
	 * @param array $config Custom configuration values
	 */
	public function setConfig( array $config )
	{
		$this->_values['config'] = $config;
		$this->setModified();
	}


	/**
	 * Returns the position of the plugin item.
	 *
	 * @return integer Position of the item
	 */
	public function getPosition()
	{
		return ( isset( $this->_values['pos'] ) ? (int) $this->_values['pos'] : 0 );
	}


	/**
	 * Sets the new position of the plugin item.
	 *
	 * @param integer $position Position of the item
	 */
	public function setPosition($position)
	{
		if ( $position == $this->getPosition() ) { return; }

		$this->_values['pos'] = (int) $position;
		$this->setModified();
	}


	/**
	 * Returns the status of the plugin item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the new status of the plugin item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
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
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['plugin.type'] = $this->getType();
		$list['plugin.typeid'] = $this->getTypeId();
		$list['plugin.label'] = $this->getLabel();
		$list['plugin.provider'] = $this->getProvider();
		$list['plugin.config'] = $this->getConfig();
		$list['plugin.status'] = $this->getStatus();
		$list['plugin.position'] = $this->getPosition();

		return $list;
	}

}
