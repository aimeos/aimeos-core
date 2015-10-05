<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs;


/**
 * Common methods for Jobs controller classes.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Base
{
	private $aimeos;
	private $context;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param \Aimeos\Aimeos $aimeos \Aimeos\Aimeos main object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Aimeos $aimeos )
	{
		$this->context = $context;
		$this->aimeos = $aimeos;
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the \Aimeos\Aimeos object.
	 *
	 * @return \Aimeos\Aimeos \Aimeos\Aimeos object
	 */
	protected function getAimeos()
	{
		return $this->aimeos;
	}


	/**
	 * Returns the absolute path to the given template file.
	 * It uses the first one found from the configured paths in the manifest files, but in reverse order.
	 *
	 * @param string|array $default Relative file name or list of file names to use when nothing else is configured
	 * @param string $confpath Configuration key of the path to the template file
	 * @return string path the to the template file
	 * @throws \Aimeos\Controller\Jobs\Exception If no template file was found
	 */
	protected function getTemplate( $confpath, $default )
	{
		$ds = DIRECTORY_SEPARATOR;
		$templatePaths = $this->aimeos->getCustomPaths( 'controller/jobs/layouts' );

		foreach( (array) $default as $fname )
		{
			$file = $this->context->getConfig()->get( $confpath, $fname );

			foreach( array_reverse( $templatePaths ) as $path => $relPaths )
			{
				foreach( $relPaths as $relPath )
				{
					$absPath = $path . $ds . $relPath . $ds . $file;
					if( $ds !== '/' ) {
						$absPath = str_replace( '/', $ds, $absPath );
					}

					if( is_file( $absPath ) ) {
						return $absPath;
					}
				}
			}
		}

		throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Template "%1$s" not available', $file ) );
	}


	/**
	 * Returns the attribute type item specified by the code.
	 *
	 * @param string $prefix Domain prefix for the manager, e.g. "media/type"
	 * @param string $domain Domain of the type item
	 * @param string $code Code of the type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Type item
	 * @throws \Aimeos\Controller\Jobs\Exception If no item is found
	 */
	protected function getTypeItem( $prefix, $domain, $code )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $prefix );
		$prefix = str_replace( '/', '.', $prefix );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', $prefix . '.domain', $domain ),
			$search->compare( '==', $prefix . '.code', $code ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false )
		{
			$msg = sprintf( 'No type item for "%1$s/%2$s" in "%3$s" found', $domain, $code, $prefix );
			throw new \Aimeos\Controller\Jobs\Exception( $msg );
		}

		return $item;
	}
}
