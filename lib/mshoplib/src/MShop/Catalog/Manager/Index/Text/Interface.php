<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 */


/**
 * Catalog index interface for classes managing product indices.
 *
 * @package MShop
 * @subpackage Catalog
 */
interface MShop_Catalog_Manager_Index_Text_Interface extends MShop_Catalog_Manager_Index_Interface
{
	/**
	 * Returns product IDs and texts that matches the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @return array Associative list of the product ID as key and the product text as value
	 */
	public function searchTexts( MW_Common_Criteria_Interface $search );
}