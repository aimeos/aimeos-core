<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 * @version $Id: Exception.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Exception for database related operations.
 *
 * @package MW
 * @subpackage DB
 */
class MW_DB_Exception extends MW_Exception
{
	protected $_info = '';
	protected $_state = '';


	/**
	 * Initializes the exception.
	 *
	 * @param string $_message Error message
	 * @param int $_code Error code
	 * @param mixed $_info Additional error info
	 */
	public function __construct( $message, $state = '', $info = null )
	{
		parent::__construct( $message );

		$this->_state = $state;
		$this->_info = $info;
	}


	/**
	 * Returns the SQL error code.
	 *
	 * @return string SQL error code
	 */
	public function getSqlState()
	{
		return $this->_state;
	}


	/**
	 * Returns the addtional error information.
	 *
	 * @return mixed Additional error info
	 */
	public function getInfo()
	{
		return $this->_info;
	}
}
