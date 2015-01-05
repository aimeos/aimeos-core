<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	private $_arcavias;
	private $_context;


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 * @param Arcavias $arcavias Arcavias main object
	 */
	public function __construct( MShop_Context_Item_Interface $context, Arcavias $arcavias )
	{
		$this->_context = $context;
		$this->_arcavias = $arcavias;
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _getContext()
	{
		return $this->_context;
	}


	/**
	 * Returns the Arcavias object.
	 *
	 * @return Arcavias Arcavias object
	 */
	protected function _getArcavias()
	{
		return $this->_arcavias;
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
	protected function _getTemplate( $confpath, $default )
	{
		$ds = DIRECTORY_SEPARATOR;
		$templatePaths = $this->_arcavias->getCustomPaths( 'controller/jobs/layouts' );
	
		foreach( (array) $default as $fname )
		{
			$file = $this->_context->getConfig()->get( $confpath, $fname );
	
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
	protected function _getTypeItem( $prefix, $domain, $code )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), $prefix );
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
