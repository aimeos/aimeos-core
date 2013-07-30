<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */



/**
 * Implementation of the excel manager.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Common_Load_Container_PHPExcel implements Controller_ExtJS_Common_Load_Container_Interface, Iterator
{
	private $_phpExcel;
	private $_data;
	private $_position;
	private $_keys;
	private $_path;

	/**
	 * Creates file for managing entries.
	 *
	 * @param string $path Path to the file
	 */
	public function __construct( $path, $domain = null )
	{
		if( is_file( $path ) )
		{
			$this->_phpExcel = PHPExcel_IOFactory::load( $path );
			$this->_path = $path;

			$this->_keys = $this->_phpExcel->getSheetNames();
			foreach( $this->_keys as $key ) {
				$this->_data[$key] = new Controller_ExtJS_Common_Load_Content_Sheet( $this->_phpExcel->getSheetByName( $key ), $key, $this->_domain );
			}
		}
		else
		{
			$this->_path = ( $path === 'php://output' ) ? $path : $path . '.xls';

			$this->_phpExcel = new PHPExcel();
			$this->_phpExcel->getProperties()
			->setCreator( 'Arcavias' )
			->setLastModifiedBy( 'Arcavias export' )
			->setTitle( 'Arcavias ' . $domain . ' text export' )
			->setSubject( 'Arcavias ' . $domain . ' text export' )
			->setDescription( 'Export file for all ' . $domain . ' texts' )
			->setKeywords( 'export ' . $domain . ' text translation' );

			$this->_phpExcel->removeSheetByIndex( 0 );

			$this->_position = 0;
			$this->_keys = array();
		}
	}



	/**
	 * Adds data file to the container i.e. csv file.
	 *
	 * @param Controller_ExtJS_Common_Load_Content_Interface $content Content object
	 */
	public function addContent( Controller_ExtJS_Common_Load_Content_Interface $content )
	{
		$langid = $content->getLanguageId();

		if( is_null( $langid ) || isset( $this->_data[ $langid ] ) ) {
			throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'Language id null or already taken' ) );
		}

		$this->_data[ $langid ] = $content;
		$this->_keys[] = $langid;

		$this->_phpExcel->addSheet( $content->getResource() );
	}


	/**
	 * Removes content object specified by language id.
	 *
	 * @param string $langid Language id
	 */
	public function removeContent( $langid )
	{
		if( !isset( $this->_data[ $langid ] ) ) {
			throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'No language "%1$s" found in the container' ), $langid );
		}

		$this->_phpExcel->removeSheetByIndex( $this->_phpExcel->getIndex( $this->_data[ $langid ]->getResource() ) );

		foreach( $this->_keys as $id => $lang )
		{
			if( $langid === $lang )
			{
				unset( $this->_keys[$id] );
				break;
			}
		}

		unset( $this->_data[ $langid ] );
	}


	/**
	 * Creates content object for specified language id and in specified format.
	 *
	 * @param string $langid Language id
	 * @param string $format Format i.e. csv, xls
	 * @return Controller_ExtJS_Common_Load_Content_Interface $content Content object
	 */
	public function createContent( $langid, $format = '' )
	{
		return new Controller_ExtJS_Common_Load_Content_Sheet( $this->_phpExcel->createSheet(), $langid );
	}


	/**
	 * Gets one content object.
	 *
	 * @param string $langid Language id
	 */
	public function get( $langid )
	{
		if( isset( $this->_data[ $langid ] ) ) {
			return $this->_data[ $langid ];
		}

		throw new Controller_ExtJS_Common_Load_Exception( sprintf( 'No content object for the language id "%1$s"' ), $langid );
	}


	/**
	 * Cleans up and saves the file.
	 */
	public function finish()
	{
		$objWriter = PHPExcel_IOFactory::createWriter($this->_phpExcel, 'Excel5');
		$objWriter->save( $this->_path );

		return basename( $this->_path );
	}


	//iterator methods
	function rewind()
	{
		$this->_position = 0;
	}


	function current()
	{
		return $this->_data[ $this->_keys[ $this->_position ] ];
	}


	function key()
	{
		return $this->_keys[ $this->_position ];
	}


	function next()
	{
		++$this->_position;
	}


	function valid()
	{
		return isset( $this->_keys[ $this->_position ] );
	}
}