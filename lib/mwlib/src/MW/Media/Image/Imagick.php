<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
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
	private static $watermark;

	private $options;
	private $image;


	/**
	 * Initializes the new image object.
	 *
	 * @param string $content File content
	 * @param string $mimetype Mime type of the media data
	 * @param array $options Associative list of configuration options
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be retrieved from the given file name
	 */
	public function __construct( string $content, string $mimetype, array $options )
	{
		parent::__construct( $mimetype );

		try
		{
			if( !self::$watermark && isset( $options['image']['watermark'] ) )
			{
				$image = new \Imagick( [] );
				$image->readImage( $options['image']['watermark'] );

				self::$watermark = $image;
			}

			$this->image = new \Imagick( [] );
			$this->image->readImageBlob( $content );
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\Media\Exception( $e->getMessage() );
		}

		$this->options = $options;
	}


	/**
	 * Cleans up
	 */
	public function __destruct()
	{
		if( $this->image ) {
			$this->image->clear();
		}
	}


	/**
	 * Clone resources
	 */
	public function __clone()
	{
		$this->image = clone $this->image;
	}


	/**
	 * Returns the height of the image
	 *
	 * @return int Height in pixel
	 */
	public function getHeight() : int
	{
		return $this->image->getImageHeight();
	}


	/**
	 * Returns the width of the image
	 *
	 * @return int Width in pixel
	 */
	public function getWidth() : int
	{
		return $this->image->getImageWidth();
	}


	/**
	 * Stores the media data at the given file name.
	 *
	 * @param string|null $filename File name to save the data into or null to return the data
	 * @param string|null $mimetype Mime type to save the content as or null to leave the mime type unchanged
	 * @return string|null File content if file name is null or null if data is saved to the given file name
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be saved to the given file name
	 */
	public function save( string $filename = null, string $mimetype = null ) : ?string
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
			if( self::$watermark !== null ) {
				$this->watermark();
			}

			$this->image->setImageFormat( $mime[1] );
			$this->image->setImageCompressionQuality( $quality );

			if( $filename === null ) {
				return $this->image->getImageBlob();
			}

			$this->image->writeImage( $filename );
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\Media\Exception( $e->getMessage() );
		}

		return null;
	}


	/**
	 * Scales the image to the given width and height.
	 *
	 * @param int|null $width New width of the image or null for automatic calculation
	 * @param int|null $height New height of the image or null for automatic calculation
	 * @param bool $fit True to keep the width/height ratio of the image
	 * @return \Aimeos\MW\Media\Image\Iface Self object for method chaining
	 */
	public function scale( int $width, int $height, bool $fit = true ) : \Aimeos\MW\Media\Image\Iface
	{
		try
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

			$newMedia = clone $this;

			if( $fit === false && $width && $height )
			{
				$newMedia->image->cropThumbnailImage( (int) $width, (int) $height );
				// see https://www.php.net/manual/en/imagick.cropthumbnailimage.php#106710
				$newMedia->image->setImagePage( 0, 0, 0, 0 );
			}
			else
			{
				$newMedia->image->resizeImage( $width, $height, \Imagick::FILTER_CUBIC, 0.8 );
			}

			return $newMedia;
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\Media\Exception( $e->getMessage() );
		}
	}


	/**
	 * Adds the configured water mark to the image
	 */
	protected function watermark()
	{
		$ww = self::$watermark->getImageHeight();
		$wh = self::$watermark->getImageWidth();

		$ratio = min( $this->getWidth() / $ww, $this->getHeight() / $wh );
		$newHeight = (int) ( $wh * $ratio );
		$newWidth = (int) ( $ww * $ratio );

		$dx = (int) ( $this->getWidth() - $newWidth ) / 2;
		$dy = (int) ( $this->getHeight() - $newHeight ) / 2;

		$image = clone self::$watermark;
		$image->setImageColorspace( $this->image->getImageColorspace() );
		$image->resizeImage( $newWidth, $newHeight, \Imagick::FILTER_CUBIC, 0.8 );

		$this->image->compositeImage( $image, \Imagick::COMPOSITE_OVER, $dx, $dy );

		$image->clear();
	}
}
