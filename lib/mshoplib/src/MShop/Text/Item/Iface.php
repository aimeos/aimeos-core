<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\ListRef\Iface, \Aimeos\MShop\Common\Item\Typeid\Iface
{
	/**
	 * Returns the domain of the text item.
	 *
	 * @return string Domain of the text item
	 */
	public function getDomain();

	/**
	 * Sets the domain of the text item.
	 *
	 * @param string $domain Domain of the text item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setDomain( $domain );

	/**
	 * Returns the ISO language code.
	 *
	 * @return string|null ISO language code (e.g. de or de_DE)
	 */
	public function getLanguageId();

	/**
	 * Sets the ISO language code.
	 *
	 * @param string|null $langid ISO language code (e.g. de or de_DE)
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setLanguageId( $langid );

	/**
	 * Returns the content of the text item.
	 *
	 * @return string Content of the text item
	 */
	public function getContent();

	/**
	 * Sets the content of the text item.
	 *
	 * @param string $text Content of the text item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setContent( $text );

	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel();

	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setLabel( $label );

	/**
	 * Returns the status of the text item.
	 *
	 * @return integer Status of the text item
	 */
	public function getStatus();

	/**
	 * Sets the status of the text item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Text\Item\Iface Text item for chaining method calls
	 */
	public function setStatus( $status );
}
