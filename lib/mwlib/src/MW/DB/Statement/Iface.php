<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * @param integer $position Position index of the placeholder
	 * @param mixed $value Value which should be bound to the placeholder
	 * @param integer $type Type of given value defined in \Aimeos\MW\DB\Stmt\Base as constant
	 * @return void
	 */
	public function bind( $position, $value, $type = \Aimeos\MW\DB\Statement\Base::PARAM_STR);

	/**
	 * Executes the statement.
	 *
	 * @return \Aimeos\MW\DB\Result\Iface Result object
	 */
	public function execute();
}
