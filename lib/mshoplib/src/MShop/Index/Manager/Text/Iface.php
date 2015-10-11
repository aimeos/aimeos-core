<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Catalog
 */


namespace Aimeos\MShop\Index\Manager\Text;


/**
 * Catalog index interface for classes managing product indices.
 *
 * @package MShop
 * @subpackage Catalog
 */
interface Iface extends \Aimeos\MShop\Index\Manager\Iface
{
	/**
	 * Returns product IDs and texts that matches the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @return array Associative list of the product ID as key and the product text as value
	 */
	public function searchTexts( \Aimeos\MW\Criteria\Iface $search );
}