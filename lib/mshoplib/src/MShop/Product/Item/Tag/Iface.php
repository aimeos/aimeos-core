<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Item\Tag;


/**
 * Default tag item implementation
 *
 * @package MShop
 * @subpackage Product
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Typeid\Iface
{
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
	 * @return void
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
	 * @return void
	 */
	public function setLabel( $label );

}
