<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Text
 */


namespace Aimeos\MShop\Text\Item;


/**
 * Generic interface for text items created and saved by text managers.
 *
 * @package MShop
 * @subpackage Text
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Domain\Iface,
		\Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\Status\Iface,
		\Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the ISO language code.
	 *
	 * @return string|null ISO language code (e.g. de or de_DE)
	 */
	public function getLanguageId() : ?string;

	/**
	 * Sets the ISO language code.
	 *
	 * @param string|null $langid ISO language code (e.g. de or de_DE)
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setLanguageId( ?string $langid ) : \Aimeos\MShop\Text\Item\Iface;

	/**
	 * Returns the content of the text item.
	 *
	 * @return string Content of the text item
	 */
	public function getContent() : string;

	/**
	 * Sets the content of the text item.
	 *
	 * @param string $text Content of the text item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setContent( string $text ) : \Aimeos\MShop\Text\Item\Iface;

	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setLabel( ?string $label ) : \Aimeos\MShop\Text\Item\Iface;
}
