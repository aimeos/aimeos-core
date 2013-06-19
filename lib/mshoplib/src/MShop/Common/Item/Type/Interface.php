<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Generic interface for all type items.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_Type_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the code of the common list type item
	 *
	 * @return string Code of the common list type item
	 */
	public function getCode();

	/**
	 * Sets the code of the common list type item
	 *
	 * @param integer $code New code of the common list type item
	 */
	public function setCode( $code );

	/**
	 * Returns the domain of the common list type item
	 *
	 * @return string Domain of the common list type item
	 */
	public function getDomain();

	/**
	 * Sets the domain of the common list type item
	 *
	 * @param string $domain New domain of the common list type item
	 */
	public function setDomain( $domain );

	/**
	 * Returns the label of the common list type item
	 *
	 * @return string Label of the common list type item
	 */
	public function getLabel();

	/**
	 * Sets the label of the common list type item
	 *
	 * @param string $label New label of the common list type item
	 */
	public function setLabel( $label );

	/**
	 * Returns the status of the common list type item
	 *
	 * @return integer Status of the common list type item
	 */
	public function getStatus();

	/**
	 * Sets the status of the common list type item
	 *
	 * @param integer $status New status of the common list type item
	 */
	public function setStatus( $status );
}
