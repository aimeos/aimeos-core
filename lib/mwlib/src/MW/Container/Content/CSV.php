<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage Container
 */


/**
 * Implementation of the csv content object.
 *
 * @package MW
 * @subpackage Container
 */
class MW_Container_Content_CSV
	extends MW_Container_Content_Abstract
	implements MW_Container_Content_Interface
{
	private $_separator;
	private $_enclosure;
	private $_escape;
	private $_lineend;
	private $_fh;
	private $_data;
	private $_position = 0;


	/**
	 * Initializes the CSV content object.
	 *
	 * Supported options are:
	 * - csv-separator (default: ',')
	 * - csv-enclosure (default: '"')
	 * - csv-escape (default: '"')
	 * - csv-lineend (default: LF)
	 *
	 * @param resource|string $resource File pointer or path to the actual file
	 * @param string $name Name of the CSV file
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public function __construct( $resource, $name, array $options = array() )
	{
		if( !is_resource( $resource ) )
		{
			if( ( $this->_fh = @fopen( $resource, 'a+' ) ) === false
				&& ( $this->_fh = fopen( $resource, 'r' ) ) === false
			) {
				throw new MW_Container_Exception( sprintf( 'Unable to open file "%1$s"', $resource ) );
			}
		}
		else
		{
			$this->_fh = $resource;
		}

		if( substr( $name, -4 ) !== '.csv' ) {
			$name .= '.csv';
		}

		parent::__construct( $resource, $name, $options );

		$this->_separator = $this->_getOption( 'csv-separator', ',' );
		$this->_enclosure = $this->_getOption( 'csv-enclosure', '"' );
		$this->_escape = $this->_getOption( 'csv-escape', '"' );
		$this->_lineend = $this->_getOption( 'csv-lineend', chr( 10 ) );
		$this->_data = $this->_getData();
	}


	/**
	 * Closes the CSV file so it's written to disk.
	 *
	 * @throws MW_Container_Exception If the file handle couldn't be flushed or closed
	 */
	public function close()
	{
		if( fflush( $this->_fh ) === false ) {
			throw new MW_Container_Exception( sprintf( 'Unable to flush file "%1$s"', $this->_resource ) );
		}

		if( fclose( $this->_fh ) === false ) {
			throw new MW_Container_Exception( sprintf( 'Unable to close file "%1$s"', $this->_resource ) );
		}
	}


	/**
	 * Adds row to the content object.
	 *
	 * @param mixed $data Data to add
	 */
	public function add( $data )
	{
		$enclosure = $this->_enclosure;

		foreach( $data as $key => $entry ) {
			$data[$key] = $enclosure . str_replace( $enclosure, $this->_escape . $enclosure, $entry ) . $enclosure;
		}

		if( fwrite( $this->_fh, implode( $this->_separator, $data ) . $this->_lineend ) === false ) {
			throw new MW_Container_Exception( sprintf( 'Unable to add content to file "%1$s"', $this->_filename ) );
		}
	}


	/**
	 * Return the current element.
	 *
	 * @return array List of values
	 */
	function current()
	{
		return $this->_data;
	}


	/**
	 * Returns the key of the current element.
	 *
	 * @return integer Position within the CSV file
	 */
	function key()
	{
		if( $this->_data !== null ) {
			return $this->_position;
		}

		return null;
	}


	/**
	 * Moves forward to next element.
	 */
	function next()
	{
		$this->_position++;
		$this->_data = $this->_getData();
	}


	/**
	 * Rewinds the file pointer to the beginning.
	 */
	function rewind()
	{
		if( @rewind( $this->_fh ) === false )
		{
			fclose( $this->_fh );

			if( ( $this->_fh = fopen( $this->getResource(), 'r' ) ) === false ) {
				throw new MW_Container_Exception( sprintf( 'Unable to rewind %1$s', $this->_fh ) );
			}
		}

		$this->_position = 0;
		$this->_data = $this->_getData();
	}


	/**
	 * Checks if the current position is valid.
	 *
	 * @return boolean True on success or false on failure
	 */
	function valid()
	{
		return !feof( $this->_fh );
	}


	/**
	 * Reads the next line from the file.
	 *
	 * @return array List of values
	 */
	protected function _getData()
	{
		do
		{
			$data = fgetcsv( $this->_fh, 0, $this->_separator, $this->_enclosure, $this->_escape );

			if( $data === false || $data === null ) {
				return null;
			}
		}
		while( $data === array( null ) );

		return $data;
	}
}
