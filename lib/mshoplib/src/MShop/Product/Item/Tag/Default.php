<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
 */


/**
 * Default tag item implementation.
 *
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Item_Tag_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Product_Item_Tag_Interface
{
	private $_values;

	/**
	 * Initializes the tag item object with the given values
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct('product.tag.', $values);
		
		$this->_values = $values;
	}


	/**
	 * Returns the language ID of the product tag item.
	 *
	 * @return string|null Language ID of the product tag item
	 */
	public function getLanguageId()
	{
		return ( isset( $this->_values['langid'] ) ? (string) $this->_values['langid'] : null );
	}


	/**
	 *  Sets the language ID of the product tag item.
	 *
	 * @param string|null $id Language ID of the product tag item
	 */
	public function setLanguageId( $id )
	{
		if ( $id === $this->getLanguageId() ) { return; }

		if ( $id !== null ) {
			if ( strlen($id) !== 2 || ctype_alpha($id) === false ) {
				throw new MShop_Product_Exception(sprintf('Invalid language ID "%1$s"', $id));
			}

			$id = (string) $id;
		}

		$this->_values['langid'] = $id;
		$this->setModified();
	}


	/**
	 * Returns the type id of the product tag item
	 *
	 * @return integer|null Type of the product tag item
	 */
	public function getTypeId()
	{
		return ( isset( $this->_values['typeid'] ) ? (int) $this->_values['typeid'] : null );
	}


	/**
	 * Sets the new type of the product tag item
	 *
	 * @param integer|null $id Type of the product tag item
	 */
	public function setTypeId( $id )
	{
		$id = (int) $id;
		if ( $id === $this->getTypeId() ) { return; }

		$this->_values['typeid'] = (int) $id;
		$this->setModified();
	}


	/**
	 * Returns the label of the product tag item.
	 *
	 * @return string Label of the product tag item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the Label of the product tag item.
	 *
	 * @param string $label Label of the product tag item
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the type code of the product tag item.
	 *
	 * @return string Type code of the product tag item
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['product.tag.typeid'] = $this->getTypeId();
		$list['product.tag.languageid'] = $this->getLanguageId();
		$list['product.tag.label'] = $this->getLabel();
		$list['product.tag.type'] = $this->getType();

		return $list;
	}

}
