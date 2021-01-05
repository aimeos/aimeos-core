<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Domain\Iface,
		\Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the language id of the tag item
	 *
	 * @return string|null Language ID of the tag item
	 */
	public function getLanguageId() : ?string;

	/**
	 * Sets the Language Id of the tag item
	 *
	 * @param string|null $id New Language ID of the tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setLanguageId( ?string $id ) : \Aimeos\MShop\Tag\Item\Iface;

	/**
	 * Returns the label of the tag item.
	 *
	 * @return string Label of the tag item
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the tag item.
	 *
	 * @param string $label Label of the tag item
	 * @return \Aimeos\MShop\Tag\Item\Iface Tag item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Tag\Item\Iface;
}
