<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Common methods for Jobs controller classes.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Controller_Jobs_Abstract
{
	private $aimeos;
	private $context;


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 * @param Aimeos $aimeos Aimeos main object
	 */
	public function __construct( MShop_Context_Item_Interface $context, Aimeos $aimeos )
	{
		$this->context = $context;
		$this->aimeos = $aimeos;
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the Aimeos object.
	 *
	 * @return Aimeos Aimeos object
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
	 * @throws Controller_Jobs_Exception If no template file was found
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
	
		throw new Controller_Jobs_Exception( sprintf( 'Template "%1$s" not available', $file ) );
	}
	

	/**
	 * Returns the attribute type item specified by the code.
	 *
	 * @param string $prefix Domain prefix for the manager, e.g. "media/type"
	 * @param string $domain Domain of the type item
	 * @param string $code Code of the type item
	 * @return MShop_Common_Item_Type_Interface Type item
	 * @throws Controller_Jobs_Exception If no item is found
	 */
	protected function getTypeItem( $prefix, $domain, $code )
	{
		$manager = MShop_Factory::createManager( $this->getContext(), $prefix );
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
			throw new Controller_Jobs_Exception( $msg );
		}

		return $item;
	}
}
