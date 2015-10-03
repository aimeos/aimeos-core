<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Container
 */


/**
 * Implementation of the gzip content object.
 *
 * @package MW
 * @subpackage Container
 */
class MW_Container_Content_Gzip
	extends MW_Container_Content_Base
	implements MW_Container_Content_Iface
{
	private $fh;
	private $data;
	private $position = 0;


	/**
	 * Initializes the text content object.
	 *
	 * Supported options are:
	 * - gzip-level (default: 5)
	 *
	 * @param string $resource Path to the actual file
	 * @param string $name Name of the file
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public function __construct( $resource, $name, array $options = array() )
	{
		if( !is_file( $resource ) && substr( $resource, -3 ) !== '.gz' ) {
			$resource .= '.gz';
		}

		if( substr( $name, -3 ) !== '.gz' ) {
			$name .= '.gz';
		}

		$level = $this->getOption( 'gzip-level', 5 );

		if( ( $this->fh = @gzopen( $resource, 'rb' . $level ) ) === false
			&& ( $this->fh = gzopen( $resource, 'wb' ) ) === false
		) {
			throw new MW_Container_Exception( sprintf( 'Unable to open file "%1$s"', $resource ) );
		}

		parent::__construct( $resource, $name, $options );

		$this->data = $this->getData();
	}


	/**
	 * Closes the gzip file so it's written to disk.
	 *
	 * @throws MW_Container_Exception If the file handle couldn't be flushed or closed
	 */
	public function close()
	{
		if( gzclose( $this->fh ) === false ) {
			throw new MW_Container_Exception( sprintf( 'Unable to close file "%1$s"', $this->getResource() ) );
		}
	}


	/**
	 * Adds content to the gzip file.
	 *
	 * @param string[] $data Data to add
	 */
	public function add( $data )
	{
		if( gzwrite( $this->fh, $data ) === false ) {
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
		return $this->data;
	}


	/**
	 * Returns the key of the current element.
	 *
	 * @return integer|null Position within the text file or null if end of file is reached
	 */
	function key()
	{
		if( $this->data !== null ) {
			return $this->position;
		}

		return null;
	}


	/**
	 * Moves forward to next element.
	 */
	function next()
	{
		$this->position++;
		$this->data = $this->getData();
	}


	/**
	 * Rewinds the file pointer to the beginning.
	 */
	function rewind()
	{
		if( gzrewind( $this->fh ) === false ) {
			throw new MW_Container_Exception( sprintf( 'Unable to rewind file "%1$s"', $this->getResource() ) );
		}

		$this->position = 0;
		$this->data = $this->getData();
	}


	/**
	 * Checks if the current position is valid.
	 *
	 * @return boolean True on success or false on failure
	 */
	function valid()
	{
		return ( $this->data === null ? !gzeof( $this->fh ) : true );
	}


	/**
	 * Reads the next line from the file.
	 *
	 * @return String Content
	 */
	protected function getData()
	{
		if( ( $data = gzgets( $this->fh, 0x100000 ) ) === false ) {
			return null;
		}

		return $data;
	}
}
