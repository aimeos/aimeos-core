<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 * @package MW
 * @subpackage Media
 */


namespace Aimeos\MW\Media\Image;


/**
 * Default image class using ImageMagick.
 *
 * @package MW
 * @subpackage Media
 */
class Imagick
	extends \Aimeos\MW\Media\Image\Base
	implements \Aimeos\MW\Media\Image\Iface
{
	private $image;
	private $options;


	/**
	 * Initializes the new image object.
	 *
	 * @param string $content File content
	 * @param string $mimetype Mime type of the media data
	 * @param array $options Associative list of configuration options
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be retrieved from the given file name
	 */
	public function __construct( $content, $mimetype, array $options )
	{
		parent::__construct( $mimetype );

		$this->options = $options;

		try
		{
			$this->image = new \Imagick( [] );
			$this->image->readImageBlob( $content );
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\Media\Exception( $e->getMessage() );
		}
	}


	/**
	 * Stores the media data at the given file name.
	 *
	 * @param string|null $filename File name to save the data into or null to return the data
	 * @param string|null $mimetype Mime type to save the content as or null to leave the mime type unchanged
	 * @return string|null File content if file name is null or null if data is saved to the given file name
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be saved to the given file name
	 */
	public function save( $filename = null, $mimetype = null )
	{
		if( $mimetype === null ) {
			$mimetype = $this->getMimeType();
		}

		$quality = 90;
		$mime = explode( '/', $mimetype );

		if( isset( $this->options['image']['quality'] ) ) {
			$quality = max( min( (int) $this->options['image']['quality'], 100 ), 0 );
		}

		try
		{
			$this->image->setImageFormat( $mime[1] );
			$this->image->setImageCompression( 100 - $quality ); // inverse quality scheme

			if( $filename === null ) {
				return $this->image->getImageBlob();
			}

			$this->image->writeImage( $filename );
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\Media\Exception( $e->getMessage() );
		}
	}


	/**
	 * Scales the image to the given width and height.
	 *
	 * @param integer $width New width of the image
	 * @param integer $height New height of the image
	 * @param boolean $fit True to keep the width/height ratio of the image
	 * @return \Aimeos\MW\Media\Iface Self object for method chaining
	 */
	public function scale( $width, $height, $fit = true )
	{
		if( $fit === true )
		{
			$w = $this->image->getImageWidth();
			$h = $this->image->getImageHeight();

			list( $width, $height ) = $this->getSizeFitted( $w, $h, $width, $height );

			if( $w <= $width && $h <= $height ) {
				return $this;
			}
		}

		try {
			$this->image->resizeImage( $width, $height, \Imagick::FILTER_CUBIC, 0.8 );
		} catch( \Exception $e ) {
			throw new \Aimeos\MW\Media\Exception( $e->getMessage() );
		}

		return $this;
	}
}
