<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
 */


/**
 * Default implementation of the list item.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Item_Type_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Common_Item_Type_Interface
{
	private $_prefix;
	private $_values;


	/**
	 * Initializes the type item object.
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values Initial values of the list type item
	 */
	public function __construct( $prefix, array $values = array() )
	{
		parent::__construct( $prefix, $values );

		$this->_prefix = $prefix;
		$this->_values = $values;
	}


	/**
	 * Returns the code of the common list type item
	 *
	 * @return string Code of the common list type item
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the code of the common list type item
	 *
	 * @param string $code New code of the common list type item
	 */
	public function setCode( $code )
	{
		$this->_checkCode( $code );

		if ( $code == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the domain of the common list type item
	 *
	 * @return string Domain of the common list type item
	 */
	public function getDomain()
	{
		return ( isset( $this->_values['domain'] ) ? (string) $this->_values['domain'] : '' );
	}


	/**
	 * Sets the domain of the common list type item
	 *
	 * @param string $domain New domain of the common list type item
	 */
	public function setDomain( $domain )
	{
		if ( $domain == $this->getDomain() ) { return; }

		$this->_values['domain'] = (string) $domain;
		$this->setModified();
	}


	/**
	 * Returns the label of the common list type item
	 *
	 * @return string Label of the common list type item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the label of the common list type item
	 *
	 * @param string $label New label of the common list type item
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the status of the common list type item
	 *
	 * @return integer Status of the common list type item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status of the common list type item
	 *
	 * @param integer $status New status of the common list type item
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns an associative list of item properties.
	 *
	 * @return array List of item properties.
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list[$this->_prefix . 'code'] = $this->getCode();
		$list[$this->_prefix . 'domain'] = $this->getDomain();
		$list[$this->_prefix . 'label'] = $this->getLabel();
		$list[$this->_prefix . 'status'] = $this->getStatus();

		return $list;
	}
}
