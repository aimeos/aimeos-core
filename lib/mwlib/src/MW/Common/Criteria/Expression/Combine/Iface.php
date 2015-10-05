<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Common\Criteria\Expression\Combine;


/**
 * Interface for combining objects.
 *
 * @package MW
 * @subpackage Common
 */
interface Iface extends \Aimeos\MW\Common\Criteria\Expression\Iface
{
	/**
	 * Returns the list of expressions that should be combined.
	 *
	 * @return array List of expressions
	 */
	public function getExpressions();
}
