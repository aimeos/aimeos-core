<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


/**
 * Interface for sorting objects.
 *
 * @package MW
 * @subpackage Common
 */
interface MW_Common_Criteria_Expression_Sort_Iface extends MW_Common_Criteria_Expression_Iface
{
	/**
	 * Returns the name of the variable or column to sort.
	 *
	 * @return string Name of variable or column that should be compared.
	 */
	public function getName();
}
