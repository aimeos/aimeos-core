<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Container
 */


/**
 * Implementation of the text content object.
 *
 * @package MW
 * @subpackage Container
 */
class MW_Container_Content_Text
	extends MW_Container_Content_Abstract
	implements MW_Container_Content_Interface
{
	private $_fh;
	private $_data;
	private $_position = 0;
	private $_lineend;
	private $_size;


	/**
	 * Initializes the text content object.
	 *
	 * Supported options are:
	 * - text-lineend (default: LF)
	 * - text-maxsize (default: 1MB)
	 *
	 * @param string $resource Path to the actual file
	 * @param string $name Name of the file
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public function __construct( $resource, $name, array $options = array() )
	{
		if( !is_file( $resource ) && substr( $resource, -4 ) !== '.txt' ) {
			$resource .= '.txt';
		}

		if( substr( $name, -4 ) !== '.txt' ) {
			$name .= '.txt';
		}

		if( ( $this->_fh = @fopen( $resource, 'a+' ) ) === false
			&& ( $this->_fh = fopen( $resource, 'r' ) ) === false
		) {
			throw new MW_Container_Exception( sprintf( 'Unable to open file "%1$s"', $resource ) );
		}

		parent::__construct( $resource, $name, $options );

		$this->_lineend = $this->_getOption( 'text-lineend', chr( 10 ) );
		$this->_size = $this->_getOption( 'text-maxsize', 0x100000 );
		$this->_data = $this->_getData();
	}


	/**
	 * Closes the text file so it's written to disk.
	 *
	 * @throws MW_Container_Exception If the file handle couldn't be flushed or closed
	 */
	public function close()
	{
		if( fflush( $this->_fh ) === false ) {
			throw new MW_Container_Exception( sprintf( 'Unable to flush file "%1$s"', $this->getResource() ) );
		}

		if( fclose( $this->_fh ) === false ) {
			throw new MW_Container_Exception( sprintf( 'Unable to close file "%1$s"', $this->getResource() ) );
		}
	}


	/**
	 * Adds row to the content object.
	 *
	 * @param string[] $data Data to add
	 */
	public function add( $data )
	{
		if( fwrite( $this->_fh, $data . $this->_lineend ) === false ) {
			throw new MW_Container_Exception( sprintf( 'Unable to add content to file "%1$s"', $this->getName() ) );
		}
	}


	/**
	 * Return the current element.
	 *
	 * @return string Content line ending with
	 */
	function current()
	{
		return $this->_data;
	}


	/**
	 * Returns the key of the current element.
	 *
	 * @return integer|null Position within the text file or null if end of file is reached
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
		if( rewind( $this->_fh ) === 0 ) {
			throw new MW_Container_Exception( sprintf( 'Rewind file handle for %1$s failed', $this->getResource() ) );
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
		return ( $this->_data === null ? !feof( $this->_fh ) : true );
	}


	/**
	 * Reads the next line from the file.
	 *
	 * @return string Data
	 */
	protected function _getData()
	{
		if( ( $data = fgets( $this->_fh, $this->_size ) ) === false ) {
			return null;
		}

		return $data;
	}
}
