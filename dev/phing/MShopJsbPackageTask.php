<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: MShopJsbPackageTask.php 14588 2011-12-25 17:36:40Z nsendetzky $
 */


require_once 'phing/Task.php';

/**
 * Generating jsb2 package files for core and extensions.
 */
class MShopJsbPackageTask extends Task
{
	protected $_mshop;
	protected $_projectPath = '';

	/**
	 * Initializes the object.
	 */
	public function init()
	{
		$ds = DIRECTORY_SEPARATOR;
		$this->_projectPath = realpath( dirname( __FILE__ ) . $ds . '..' . $ds . '..' );

		require_once $this->_projectPath . $ds . 'MShop.php';
		spl_autoload_register( 'MShop::autoload' );

		$this->_mshop = new MShop();

		$incPath = $this->_mshop->getIncludePaths( 'lib' );
		$incPath[] = get_include_path();
		set_include_path( implode( PATH_SEPARATOR, $incPath ) );

		return true;
	}

	/**
	 * Generates JS package files given in the manifests, including extensions.
	 */
	public function main()
	{
		$ds = DIRECTORY_SEPARATOR;

		$this->_msg( 'Generating JSB2 packages' );

		$abslen = strlen( $this->_projectPath );

		foreach( $this->_mshop->getCustomPaths( 'client/extjs' ) as $base => $paths )
		{
			foreach( $paths as $path )
			{
				$jsbPath = $base . $ds . $path;

				$message = sprintf( 'Package: %1$s ', $jsbPath );
				$this->_msg( sprintf( 'Package: %1$s ', $jsbPath ) );

				if( !is_file( $jsbPath ) || !is_readable( $jsbPath ) )
				{
					$this->_msg( $message, 'failed' );
					$this->_msg( sprintf( 'No manifest file found in %1$s', $jsbPath ) );
					continue;
				}

				try
				{
					$jsbParser = new MW_Jsb2_Default( $jsbPath );
					$jsbParser->deploy( 'js' );
					$this->_msg( $message, 'done' );
				}
				catch ( Exception $e )
				{
					$this->_msg( $message, 'failed' );
					$this->_msg( sprintf( 'Error: %1$s', $e->getMessage() ) );
				}
			}
		}
	}


	/**
	 * Prints the message for the current task.
	 *
	 * @param string $msg Current message
	 * @param integer $level Indent level of the message (default: 0 )
	 */
	protected function _msg( $msg, $status = '' )
	{
		$this->log( $msg . $status );
	}
}
