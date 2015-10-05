<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Common\Criteria\Expression\Compare;


/**
 * Interface for comparing objects.
 *
 * @package MW
 * @subpackage Common
 */
interface Iface extends \Aimeos\MW\Common\Criteria\Expression\Iface
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
	 * @return string Value that the variable or column should be compared to.
	 */
	public function getValue();
}
