<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MW
 * @subpackage Container
 */


namespace Aimeos\MW\Container\Content;


/**
 * Implementation of the csv content object.
 *
 * @package MW
 * @subpackage Container
 */
class CSV
	extends \Aimeos\MW\Container\Content\Base
	implements \Aimeos\MW\Container\Content\Iface
{
	private $fh;
	private string $separator;
	private string $enclosure;
	private string $escape;
	private string $lineend;
	private ?array $data;
	private int $position = 0;


	/**
	 * Initializes the CSV content object.
	 *
	 * Supported options are:
	 * - csv-separator (default: ',')
	 * - csv-enclosure (default: '"')
	 * - csv-escape (default: '"')
	 * - csv-lineend (default: LF)
	 *
	 * @param string $resource Path to the actual file
	 * @param string $name Name of the CSV file
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public function __construct( string $resource, string $name, array $options = [] )
	{
		if( !is_file( $resource ) && substr( $resource, -4 ) !== '.csv' ) {
			$resource .= '.csv';
		}

		if( substr( $name, -4 ) !== '.csv' ) {
			$name .= '.csv';
		}

		if( ( $this->fh = @fopen( $resource, 'a+' ) ) === false
			&& ( $this->fh = fopen( $resource, 'r' ) ) === false
		) {
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Unable to open file "%1$s"', $resource ) );
		}

		parent::__construct( $resource, $name, $options );

		$this->separator = $this->getOption( 'csv-separator', ',' );
		$this->enclosure = $this->getOption( 'csv-enclosure', '"' );
		$this->escape = $this->getOption( 'csv-escape', '"' );
		$this->lineend = $this->getOption( 'csv-lineend', "\n" );
		$this->data = $this->getData();
	}


	/**
	 * Adds row to the content object.
	 *
	 * @param string[] $data Data to add
	 * @return \Aimeos\MW\Container\Content\Iface Container content instance for method chaining
	 */
	public function add( $data ) : Iface
	{
		$list = [];
		$data = (array) $data;
		$max = max( array_keys( $data ) );
		$enclosure = $this->enclosure;

		if( is_int( $max ) ) {
			$list = array_fill( 0, $max, '' );
		}

		foreach( $data as $pos => $entry ) { // ltrim to invalidate Excel macros
			$list[$pos] = $enclosure . str_replace( $enclosure, $this->escape . $enclosure, ltrim( (string) $entry, '@=+-' ) ) . $enclosure;
		}

		if( fwrite( $this->fh, implode( $this->separator, $list ) . $this->lineend ) === false ) {
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Unable to add content to file "%1$s"', $this->getName() ) );
		}

		return $this;
	}


	/**
	 * Closes the CSV file so it's written to disk.
	 *
	 * @throws \Aimeos\MW\Container\Exception If the file handle couldn't be flushed or closed
	 * @return \Aimeos\MW\Container\Content\Iface Container content instance for method chaining
	 */
	public function close() : Iface
	{
		if( fflush( $this->fh ) === false ) {
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Unable to flush file "%1$s"', $this->getResource() ) );
		}

		if( fclose( $this->fh ) === false ) {
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Unable to close file "%1$s"', $this->getResource() ) );
		}

		return $this;
	}


	/**
	 * Return the current element.
	 *
	 * @return array|null List of values
	 */
	#[\ReturnTypeWillChange]
	public function current()
	{
		return $this->data;
	}


	/**
	 * Returns the key of the current element.
	 *
	 * @return integer|null Position within the CSV file or null if end of file is reached
	 */
	#[\ReturnTypeWillChange]
	public function key()
	{
		if( $this->data !== null ) {
			return $this->position;
		}

		return null;
	}


	/**
	 * Moves forward to next element.
	 */
	public function next() : void
	{
		$this->position++;
		$this->data = $this->getData();
	}


	/**
	 * Rewinds the file pointer to the beginning.
	 */
	public function rewind() : void
	{
		$filename = $this->getResource();

		if( fclose( $this->fh ) === false ) {
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Unable to close file handle for %1$s', $filename ) );
		}

		if( ( $this->fh = fopen( $filename, 'r' ) ) === false ) {
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Unable to open file %1$s', $filename ) );
		}

		$this->position = 0;
		$this->data = $this->getData();
	}


	/**
	 * Checks if the current position is valid.
	 *
	 * @return bool True on success or false on failure
	 */
	public function valid() : bool
	{
		return ( $this->data === null ? !feof( $this->fh ) : true );
	}


	/**
	 * Reads the next line from the file.
	 *
	 * @return array|null List of values
	 */
	protected function getData() : ?array
	{
		do
		{
			$data = fgetcsv( $this->fh, 0, $this->separator, $this->enclosure, $this->escape );

			if( $data === false || $data === null ) {
				return null;
			}
		}
		while( $data === array( null ) );

		return $data;
	}
}
