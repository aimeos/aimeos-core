<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Media
 */


/**
 * Default application media class.
 *
 * @package MW
 * @subpackage Media
 */
class MW_Media_Application_Default
	extends MW_Media_Abstract
	implements MW_Media_Application_Interface
{
	private $options;
	private $filename;


	/**
	 * Initializes the new image object.
	 *
	 * @param string $filename Name of the media file
	 * @param string $mimetype Mime type of the media data
	 * @param array $options Associative list of configuration options
	 * @throws MW_Media_Exception If image couldn't be retrieved from the given file name
	 */
	public function __construct( $filename, $mimetype, array $options )
	{
		parent::__construct( $mimetype );

		$this->filename = $filename;
		$this->options = $options;
	}


	/**
	 * Stores the media data at the given file name.
	 *
	 * @param string $filename Name of the file to save the media data into
	 * @param string $mimetype Mime type to save the image as
	 * @throws MW_Media_Exception If image couldn't be saved to the given file name
	 */
	public function save( $filename, $mimetype )
	{
		if( $this->filename != $filename && copy( $this->filename, $filename ) !== true ) {
			throw new MW_Media_Exception( sprintf( 'Unable to copy "%1$s" to "%2$s"', $this->filename, $filename ) );
		}
	}
}
