<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage PrTagoduct
 */


namespace Aimeos\MShop\Tag\Item;


/**
 * Default tag item implementation
 *
 * @package MShop
 * @subpackage Tag
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Typeid\Iface
{
	/**
	 * Returns the domain of the tag item.
	 *
	 * @return string Domain of the tag item
	 */
	public function getDomain();

	/**
	 * Sets the domain of the tag item.
	 *
	 * @param string $domain Domain of the tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setDomain( $domain );

	/**
	 * Returns the language id of the tag item
	 *
	 * @return string Language ID of the tag item
	 */
	public function getLanguageId();

	/**
	 * Sets the Language Id of the tag item
	 *
	 * @param string $id New Language ID of the tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setLanguageId( $id );

	/**
	 * Returns the label of the tag item.
	 *
	 * @return string Label of the tag item
	 */
	public function getLabel();

	/**
	 * Sets the new label of the tag item.
	 *
	 * @param string $label Label of the tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setLabel( $label );

}
