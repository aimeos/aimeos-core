<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Statement;


/**
 * Required methods for all database statement objects.
 *
 * @package MW
 * @subpackage DB
 */
interface Iface
{
	/**
	 * Binds a value to a parameter in the statement.
	 *
	 * @param int $position Position index of the placeholder
	 * @param mixed $value Value which should be bound to the placeholder
	 * @param int $type Type of given value defined in \Aimeos\MW\DB\Stmt\Base as constant
	 * @return \Aimeos\MW\DB\Statement\Iface Statement instance for method chaining
	 */
	public function bind( int $position, $value, int $type = \Aimeos\MW\DB\Statement\Base::PARAM_STR ) : \Aimeos\MW\DB\Statement\Iface;

	/**
	 * Executes the statement.
	 *
	 * @return \Aimeos\MW\DB\Result\Iface Result object
	 */
	public function execute() : \Aimeos\MW\DB\Result\Iface;
}
