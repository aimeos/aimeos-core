<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Item\Site;


/**
 * Common interface for all Site items.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface
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
	 * @param string $code The code to set
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
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
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
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
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
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
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setStatus( $status );

	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param integer $index Index of child node
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Selected node
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
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $item Child node to add
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function addChild( \Aimeos\MShop\Locale\Item\Site\Iface $item );
}
