<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Common interface for all Site items.
 *
 * @package MShop
 * @subpackage Locale
 */
interface MShop_Locale_Item_Site_Interface
	extends MShop_Common_Item_Interface
{
	/**
	 * Returns the code of the Site.
	 *
	 * @return string|null Returns the code of the item
	 */
	public function getCode();


	/**
	 * Sets the code of the Site.
	 *
	 * @param string $key The code to set
	 */
	public function setCode( $code );


	/**
	 * Returns the label property of the Site.
	 *
	 * @return string Returns the label of the Site
	 */
	public function getLabel();


	/**
	 * Sets the label property of the Site.
	 *
	 * @param string $label The label of the Site
	 */
	public function setLabel( $label );


	/**
	 * Returns the config property of the Site.
	 *
	 * @return array Returns the config of the Site
	 */
	public function getConfig();


	/**
	 * Sets the config property of the Site.
	 *
	 * @param array $options Options to be set for the Site
	 */
	public function setConfig( array $options );


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();


	/**
	 * Sets the status of the item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status );

	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param integer $index Index of child node
	 * @return MShop_Locale_Item_Site_Interface Selected node
	 */
	public function getChild( $index );

	/**
	 * Returns all children of this node.
	 *
	 * @return array Numerically indexed list of nodes
	 */
	public function getChildren();

	/**
	 * Tests if a node has children.
	 *
	 * @return boolean True if node has children, false if not
	 */
	public function hasChildren();

	/**
	 * Adds a child node to this node.
	 *
	 * @param MShop_Locale_Item_Site_Interface $item Child node to add
	 */
	public function addChild( MShop_Locale_Item_Site_Interface $item );
}
