<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression\Combine;


/**
 * Interface for combining objects.
 *
 * @package MW
 * @subpackage Common
 */
interface Iface extends \Aimeos\MW\Criteria\Expression\Iface
{
	/**
	 * Returns the list of expressions that should be combined.
	 *
	 * @return array List of expressions
	 */
	public function getExpressions();
}
