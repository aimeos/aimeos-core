<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Common\Criteria\Expression\Sort;


/**
 * Interface for sorting objects.
 *
 * @package MW
 * @subpackage Common
 */
interface Iface extends \Aimeos\MW\Common\Criteria\Expression\Iface
{
	/**
	 * Returns the name of the variable or column to sort.
	 *
	 * @return string Name of variable or column that should be compared.
	 */
	public function getName();
}
