<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Type;


/**
 * Generic interface for all type items.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
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
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
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
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setDomain( $domain );

	/**
	 * Returns the translated name for the type item
	 *
	 * @return string Translated name of the type item
	 */
	public function getName();

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
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
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
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setStatus( $status );
}
