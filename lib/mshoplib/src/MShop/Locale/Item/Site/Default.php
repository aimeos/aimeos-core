<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
 */


/**
 * Default implementation of a Site item.
 *
 * @package MShop
 * @subpackage Locale
 */
class MShop_Locale_Item_Site_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Locale_Item_Site_Interface
{
	private $_children;
	private $_values;

	/**
	 * Initializes the site object.
	 *
	 * @param array $values Possible params to be set on initialization
	 */
	public function __construct( array $values = array( ), array $children = array() )
	{
		MW_Common_Abstract::checkClassList( 'MShop_Locale_Item_Site_Interface', $children );
		parent::__construct('locale.site.', $values);

		$this->_values = $values;
		$this->_children = $children;
	}


	/**
	 * Returns the id of the site.
	 *
	 * @return integer Id of the site
	 */
	public function getSiteId()
	{
		return parent::getId();
	}


	/**
	 * Returns the code of the site.
	 *
	 * @return string|null Returns the code of the item
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the code of the site.
	 *
	 * @param string $code The code to set
	 */
	public function setCode( $code )
	{
		$this->_checkCode( $code );

		if ( $code == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the label property of the site.
	 *
	 * @return string Returns the label of the Site
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the label property of the site.
	 *
	 * @param string $label The label of the Site
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the config property of the site.
	 *
	 * @return array Returns the config of the Site
	 */
	public function getConfig()
	{
		return ( isset( $this->_values['config'] ) ? $this->_values['config'] : array() );
	}


	/**
	 * Sets the config property of the site.
	 *
	 * @param array $options Options to be set for the Site
	 */
	public function setConfig( array $options )
	{
		$this->_values['config'] = $options;
		$this->setModified();
	}


	/**
	 * Returns the status property of the Site.
	 *
	 * @return integer Returns the status of the Site
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets status property.
	 *
	 * @param integer $status The status of the Site
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['locale.site.siteid'] = $this->getSiteId();
		$list['locale.site.code'] = $this->getCode();
		$list['locale.site.label'] = $this->getLabel();
		$list['locale.site.config'] = $this->getConfig();
		$list['locale.site.status'] = $this->getStatus();

		return $list;
	}


	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param integer $index Index of child node
	 * @return MShop_Locale_Item_Site_Interface Selected node
	 */
	public function getChild( $index )
	{
		if( isset( $this->_children[$index] ) ) {
			return $this->_children[$index];
		}

		throw new MShop_Locale_Exception( sprintf( 'Child node with index "%1$d" not available', $index ) );
	}


	/**
	 * Returns all children of this node.
	 *
	 * @return array Numerically indexed list of nodes
	 */
	public function getChildren()
	{
		return $this->_children;
	}


	/**
	 * Tests if a node has children.
	 *
	 * @return boolean True if node has children, false if not
	 */
	public function hasChildren()
	{
		return ( count( $this->_children ) > 0 ? true : false );
	}


	/**
	 * Adds a child node to this node.
	 *
	 * @param MShop_Locale_Item_Site_Interface $item Child node to add
	 */
	public function addChild( MShop_Locale_Item_Site_Interface $item )
	{
		// don't set the modified flag as it's only for the values
		$this->_children[] = $item;
	}
}