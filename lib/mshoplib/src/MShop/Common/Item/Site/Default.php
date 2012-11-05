<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Default.php 1053 2012-07-31 10:55:09Z doleiynyk $
 */


/**
 * Default implementation of the site item.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Item_Site_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Common_Item_Site_Interface
{
	private $_prefix;
	private $_values;
	
	/**
	 * Initializes the site item object.
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values Initial values of the list item
	 */
	public function __construct( $prefix, array $values = array( ) )
	{
		parent::__construct($prefix, $values);
	
		$this->_values = $values;
		$this->_prefix = $prefix;
	}
	
	
	/**
	* Returns the parentid of the common site item.
	*
	* @return integer Parentid of the common site item
	*/
	public function getParentId()
	{
		return ( isset( $this->_values['parentid'] ) ? (int) $this->_values['parentid'] : null );
	}
	
	
	/**
	 * Sets the parentid of the common site item.
	 *
	 * @param integer $parentid New parentid of the common site item
	 */
	public function setParentId( $parentid )
	{
		if ( $parentid == $this->getParentId() ) {
			return;
		}
	
		$this->_values['parentid'] = (int) $parentid;
		$this->setModified();
	}
	
	
	/**
	 * Returns the value of the common site item.
	 *
	 * @return integer Value of the common site item
	 */
	public function getValue()
	{
		return ( isset( $this->_values['value'] ) ? (int) $this->_values['value'] : 0 );
	}
	
	
	/**
	 * Sets the value of the common site item.
	 *
	 * @param integer $value New value of the common site item
	 */
	public function setValue( $value )
	{
		if ( $value == $this->getValue() ) {
			return;
		}
	
		$this->_values['value'] = (int) $value;
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
	
		$list[$this->_prefix . 'parentid'] = $this->getParentId();
		$list[$this->_prefix . 'value'] = $this->getValue();
	
		return $list;
	}
	
	
}