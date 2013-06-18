<?php


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Abstract class for all delivery provider implementations.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class MShop_Service_Provider_Delivery_Abstract
extends MShop_Service_Provider_Abstract
implements MShop_Service_Provider_Delivery_Interface
{
	/**
	 * Feature constant if querying for status updates for an order is supported.
	 */
	const FEAT_QUERY = 1;
	
	const ERR_OK = 0;
	const ERR_TEMP = 1;
	const ERR_XML = 10;
	const ERR_SCHEMA = 11;
}