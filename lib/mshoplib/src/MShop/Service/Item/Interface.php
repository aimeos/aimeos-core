<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Generic interface for delivery and payment item DTOs.
 * @package MShop
 * @subpackage Service
 */
interface MShop_Service_Item_Interface
	extends MShop_Common_Item_Interface, MShop_Common_Item_ListRef_Interface,
		MShop_Common_Item_Position_Interface, MShop_Common_Item_Typeid_Interface
{
	/**
	 * Returns the code of the service item.
	 *
	 * @return string Service item code
	 */
	public function getCode();

	/**
	 * Sets a new code for the service item.
	 *
	 * @param string $code Code as defined by the service provider
	 */
	public function setCode( $code );

	/**
	 * Returns the name of the service provider the item belongs to.
	 *
	 * @return string Name of the service provider
	 */
	public function getProvider();

	/**
	 * Sets the new name of the service provider the item belongs to.
	 *
	 * @param string $provider Name of the service provider
	 */
	public function setProvider( $provider );

	/**
	 * Returns the label of the service item.
	 *
	 * @return string Service item label
	 */
	public function getLabel();

	/**
	 * Sets a new label for the service item.
	 *
	 * @param string $label Label as defined by the service provider
	 */
	public function setLabel( $label );

	/**
	 * Returns the configuration values of the item
	 *
	 * @return array Configuration values
	 */
	public function getConfig();

	/**
	 * Sets the configuration values of the item.
	 *
	 * @param array $config Configuration values
	 */
	public function setConfig( array $config );

	/**
	 * Returns the status of the service item.
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
}
