<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


/**
 * Interface for comparing objects.
 *
 * @package MW
 * @subpackage Common
 */
interface MW_Common_Criteria_Expression_Compare_Interface extends MW_Common_Criteria_Expression_Interface
{
	/**
	 * Returns the left side of the compare expression.
	 *
	 * @return string Name of variable or column that should be compared.
	 */
	public function getName();


	/**
	 * Returns the right side of the compare expression.
	 *
	 * @return mixed Value that the variable or column should be compared to.
	 */
	public function getValue();
}
