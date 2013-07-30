<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * Interface to manage excel sheet content.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Common_Load_Content_Sheet implements Controller_ExtJS_Common_Load_Content_Interface, Iterator
{
	private $_sheet;
	private $_langid;
	private $_data;
	private $_sheetLine;
	private $_maxRow;

	/**
	 * Initialize manager for content entries.
	 *
	 * @param string $path Path to the result file or sheet
	 * @param string $langid Language id for filename or title
	 */
	public function __construct( $path, $langid )
	{
		$this->_sheet = $path;
		$this->_sheet->setTitle( $langid );
		$this->_langid = $langid;
		$this->_sheetLine = 1;
		$this->_maxRow = $this->_sheet->getHighestRow();
	}

	/**
	 * Adds row to the content object.
	 *
	 * @param array $data Data to add
	 */
	public function addRow( array $data )
	{
		if( $this->_sheetLine === 1 ) {
			$this->_createTitle( $data );
		}
		else
		{
			for( $i = 0; $i<count( $data ); $i++ ) {
				$this->_sheet->setCellValueByColumnAndRow( $i, $this->_sheetLine, $data[$i] );
			}
		}

		$this->_sheetLine++;
		$this->_maxRow = $this->_sheet->getHighestRow();
	}


	/**
	 * Gets path of actual file.
	 */
	public function getResource()
	{
		return $this->_sheet;
	}


	/**
	 * Gets language id of actual content object.
	 */
	public function getLanguageId()
	{
		return $this->_langid;
	}


	//iterator methods
	function rewind()
	{
		$this->_sheetLine = 1;
		$this->_data = $this->_readLine();
	}


	function current()
	{
		return $this->_data ? $this->_data : null;
	}


	function key()
	{
		return $this->_sheetLine;
	}


	function next()
	{
		if( ($this->_sheetLine + 1) <= $this->_maxRow ) {
			$this->_sheetLine++;
			$this->_data = $this->_readLine();
		} else {
			$this->_data = false;
		}
	}


	function valid()
	{
		return $this->_data ? true : false;
	}


	protected function _createTitle( array $data )
	{
		$style = $this->_sheet->getDefaultStyle();
		$style->getAlignment()->setWrapText(true);
		$style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		$length = count( $data );

		$char = 'A';
		for( $i = 0; $i<$length-1; $i++ ) {
			$this->_sheet->getColumnDimension($char++)->setAutoSize(true);
		}
		$this->_sheet->getColumnDimension($char)->setWidth(60);

		$this->_sheet->getStyle('A1:'.$char.'1')->getFont()->setBold(true);

		for( $i = 0; $i<count( $data ); $i++ ) {
			$this->_sheet->setCellValueByColumnAndRow( $i, 1, $data[$i] );
		}
	}


	/**
	 * Reads cells from actual sheet line.
	 */
	protected function _readLine()
	{
		$line = array();
		$maxI = $this->_sheet->getHighestColumn();

		for( $i='A'; $i <= $maxI; $i++ ) {
			$line[] = $this->_sheet->getCell( $i.$this->_sheetLine )->getValue();
		}

		return $line;
	}
}