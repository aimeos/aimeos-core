<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression\Sort;


/**
 * Interface for sorting objects.
 *
 * @package MW
 * @subpackage Common
 */
interface Iface extends \Aimeos\MW\Criteria\Expression\Iface
{
	/**
	 * Returns the name of the variable or column to sort.
	 *
	 * @return string Name of variable or column that should be compared.
	 */
	public function getName();
}
