<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */



/**
 * Implementation of the csv manager.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Common_Load_Content_CSV implements Controller_ExtJS_Common_Load_Content_Interface, Iterator
{
	private $_path;
	private $_langid;
	private $_fh;
	private $_position;
	private $_data;


	/**
	 * Initialize manager for content entries.
	 *
	 * @param string $path Path to the result file
	 * @param string $langid Language id for filename or title
	 */
	public function __construct( $path, $langid )
	{
		if( is_file( $path ) ) {
			$this->_fh = fopen( $path, 'r' );
		} else {
			$this->_fh = fopen( $path, 'w+' );
		}

		$this->_position = 0;
		$this->_path = $path;
		$this->_langid = $langid;
		$this->_data = true;
	}


	/**
	 * Adds row to the content object.
	 *
	 * @param array $data Data to add
	 */
	public function addRow( array $data )
	{
		fputcsv( $this->_fh, $data );
	}


	/**
	 * Gets path of actual file.
	 */
	public function getResource()
	{
		return $this->_path;
	}


	/**
	 * Gets language id of actual content object.
	 */
	public function getLanguageId()
	{
		return $this->_langid;
	}


	//iterator methods
	function rewind()
	{
		$this->_position = 0;
		rewind( $this->_fh );
		$this->_data = fgetcsv( $this->_fh );
	}

	function current()
	{
		return $this->_data ? $this->_data : null;
	}

	function key()
	{
		return $this->_position;
	}

	function next()
	{
		++$this->_position;
		$this->_data = fgetcsv( $this->_fh );
	}

	function valid()
	{
		if(!$this->_data)
		{
			--$this->_position;
			fclose( $this->_fh );
		}
		return $this->_data ? true : false;
	}
}