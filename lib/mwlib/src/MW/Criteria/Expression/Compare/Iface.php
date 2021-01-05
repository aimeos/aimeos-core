<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression\Compare;


/**
 * Interface for comparing objects.
 *
 * @package MW
 * @subpackage Common
 */
interface Iface extends \Aimeos\MW\Criteria\Expression\Iface
{
	/**
	 * Returns the left side of the compare expression.
	 *
	 * @return string Name of variable or column that should be compared.
	 */
	public function getName() : string;


	/**
	 * Returns the right side of the compare expression.
	 *
	 * @return mixed Value that the variable or column should be compared to.
	 */
	public function getValue();
}
