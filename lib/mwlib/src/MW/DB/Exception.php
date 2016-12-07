<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB;


/**
 * \Exception for database related operations.
 *
 * @package MW
 * @subpackage DB
 */
class Exception extends \Aimeos\MW\Exception
{
	protected $info;
	protected $state;


	/**
	 * Initializes the exception.
	 *
	 * @param string $message Error message
	 * @param string $state SQL error code
	 * @param string $info Additional error info
	 */
	public function __construct( $message, $state = '', $info = '' )
	{
		parent::__construct( $message );

		$this->state = $state;
		$this->info = $info;
	}


	/**
	 * Returns the SQL error code.
	 *
	 * @return string SQL error code
	 */
	public function getSqlState()
	{
		return $this->state;
	}


	/**
	 * Returns the addtional error information.
	 *
	 * @return string Additional error info
	 */
	public function getInfo()
	{
		return $this->info;
	}
}
