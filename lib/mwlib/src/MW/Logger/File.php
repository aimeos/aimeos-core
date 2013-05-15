<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Logger
 */


/**
 * Log messages.
 *
 * @package MW
 * @subpackage Logger
 */
class MW_Logger_File extends MW_Logger_Abstract implements MW_Logger_Interface
{
	/**
	 * @var array of priorities where the keys are the
	 * priority numbers and the values are the priority names
	 */
	protected $_priorities = array();

	/**
	 * @var array of Zend_Log_Writer_Abstract
	 */
	protected $_writers = array();

	/**
	 * @var array of Zend_Log_Filter_Interface
	 */
	protected $_filters = array();


	private $_logger = null;


	/**
	 * Initializes the logger object.
	 *
	 * @param string $prefix Prefix specified by site code
	 * @param integer $priority Default priority
	 */
	public function __construct( $prefix, $priority )
	{
		$this->_logger = $logger;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @throws MW_Logger_Exception If an error occurs in Zend_Log
	 * @see MW_Logger_Abstract for available log level constants
	 */
	public function log( $message, $priority = MW_Logger_Abstract::ERR, $facility = 'message' )
	{
		try
		{
			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			$this->_log( '<' . $facility . '> ' . $message, $priority );
		}
		catch(Zend_Log_Exception $ze) {
			throw new MW_Logger_Exception($ze->getMessage());
		}
	}


	protected function _log()
	{
		// sanity checks
		if (empty($this->_writers)) {
			/** @see Zend_Log_Exception */
			require_once 'Zend/Log/Exception.php';
			throw new Zend_Log_Exception('No writers were added');
		}

		if (! isset($this->_priorities[$priority])) {
			/** @see Zend_Log_Exception */
			require_once 'Zend/Log/Exception.php';
			throw new Zend_Log_Exception('Bad log priority');
		}

		// pack into event required by filters and writers
		$event = $this->_packEvent($message, $priority);

		// Check to see if any extra information was passed
		if (!empty($extras)) {
			$info = array();
			if (is_array($extras)) {
				foreach ($extras as $key => $value) {
					if (is_string($key)) {
						$event[$key] = $value;
					} else {
						$info[] = $value;
					}
				}
			} else {
				$info = $extras;
			}
			if (!empty($info)) {
				$event['info'] = $info;
			}
		}

		// abort if rejected by the global filters
		foreach ($this->_filters as $filter) {
			if (! $filter->accept($event)) {
				return;
			}
		}

		// send to each writer
		foreach ($this->_writers as $writer) {
			$writer->write($event);
		}
	}
}
