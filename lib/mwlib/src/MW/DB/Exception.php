<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Exception for database related operations.
 *
 * @package MW
 * @subpackage DB
 */
class MW_DB_Exception extends MW_Exception
{
	protected $info;
	protected $state;


	/**
	 * Initializes the exception.
	 *
	 * @param string $message Error message
	 * @param integer $state SQL error code
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
