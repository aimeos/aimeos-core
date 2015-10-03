<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Catalog
 */


/**
 * Catalog index interface for classes managing product indices.
 *
 * @package MShop
 * @subpackage Catalog
 */
interface MShop_Catalog_Manager_Index_Text_Iface extends MShop_Catalog_Manager_Index_Iface
{
	/**
	 * Returns product IDs and texts that matches the given criteria.
	 *
	 * @param MW_Common_Criteria_Iface $search Search criteria
	 * @return array Associative list of the product ID as key and the product text as value
	 */
	public function searchTexts( MW_Common_Criteria_Iface $search );
}