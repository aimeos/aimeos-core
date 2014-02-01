<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Text
 */


/**
 * Generic interface for text items created and saved by text managers.
 *
 * @package MShop
 * @subpackage Text
 */
interface MShop_Text_Item_Interface
	extends MShop_Common_Item_Interface, MShop_Common_Item_ListRef_Interface, MShop_Common_Item_Typeid_Interface
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
	 * @throws MShop_Exception If the language ID is invalid
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
	 */
	public function setStatus( $status );

}
