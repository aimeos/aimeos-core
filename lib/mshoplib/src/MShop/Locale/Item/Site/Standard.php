<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Item\Site;


/**
 * Default implementation of a Site item.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Locale\Item\Site\Iface
{
	private $children;
	private $values;

	/**
	 * Initializes the site object.
	 *
	 * @param array $values Associative list of item key/value pairs
	 * @param array $children List of nodes implementing \Aimeos\MW\Tree\Node\Iface
	 */
	public function __construct( array $values = [], array $children = [] )
	{
		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Locale\\Item\\Site\\Iface', $children );
		parent::__construct( 'locale.site.', $values );

		$this->values = $values;
		$this->children = $children;
	}


	/**
	 * Returns the id of the site.
	 *
	 * @return integer|null Id of the site
	 */
	public function getSiteId()
	{
		return (int) $this->getId();
	}


	/**
	 * Returns the code of the site.
	 *
	 * @return string Returns the code of the item
	 */
	public function getCode()
	{
		if( isset( $this->values['locale.site.code'] ) ) {
			return (string) $this->values['locale.site.code'];
		}

		return '';
	}


	/**
	 * Sets the code of the site.
	 *
	 * @param string $code The code to set
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setCode( $code )
	{
		if( $code == $this->getCode() ) { return $this; }

		$this->values['locale.site.code'] = (string) $this->checkCode( $code );;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the label property of the site.
	 *
	 * @return string Returns the label of the Site
	 */
	public function getLabel()
	{
		if( isset( $this->values['locale.site.label'] ) ) {
			return (string) $this->values['locale.site.label'];
		}

		return '';
	}


	/**
	 * Sets the label property of the site.
	 *
	 * @param string $label The label of the Site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return $this; }

		$this->values['locale.site.label'] = (string) $label;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the config property of the site.
	 *
	 * @return array Returns the config of the Site
	 */
	public function getConfig()
	{
		if( isset( $this->values['locale.site.config'] ) ) {
			return (array) $this->values['locale.site.config'];
		}

		return [];
	}


	/**
	 * Sets the config property of the site.
	 *
	 * @param array $options Options to be set for the Site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setConfig( array $options )
	{
		$this->values['locale.site.config'] = $options;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the status property of the Site.
	 *
	 * @return integer Returns the status of the Site
	 */
	public function getStatus()
	{
		if( isset( $this->values['locale.site.status'] ) ) {
			return (int) $this->values['locale.site.status'];
		}

		return 0;
	}


	/**
	 * Sets status property.
	 *
	 * @param integer $status The status of the Site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return $this; }

		$this->values['locale.site.status'] = (int) $status;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'locale/site';
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = [];
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'locale.site.code': $this->setCode( $value ); break;
				case 'locale.site.label': $this->setLabel( $value ); break;
				case 'locale.site.config': $this->setConfig( $value ); break;
				case 'locale.site.status': $this->setStatus( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['locale.site.siteid'] = $this->getSiteId();
		$list['locale.site.code'] = $this->getCode();
		$list['locale.site.label'] = $this->getLabel();
		$list['locale.site.config'] = $this->getConfig();
		$list['locale.site.status'] = $this->getStatus();

		return $list;
	}


	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param integer $index Index of child node
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Selected node
	 */
	public function getChild( $index )
	{
		if( isset( $this->children[$index] ) ) {
			return $this->children[$index];
		}

		throw new \Aimeos\MShop\Locale\Exception( sprintf( 'Child node with index "%1$d" not available', $index ) );
	}


	/**
	 * Returns all children of this node.
	 *
	 * @return array Numerically indexed list of nodes
	 */
	public function getChildren()
	{
		return $this->children;
	}


	/**
	 * Tests if a node has children.
	 *
	 * @return boolean True if node has children, false if not
	 */
	public function hasChildren()
	{
		return ( count( $this->children ) > 0 ? true : false );
	}


	/**
	 * Adds a child node to this node.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $item Child node to add
	 */
	public function addChild( \Aimeos\MShop\Locale\Item\Site\Iface $item )
	{
		// don't set the modified flag as it's only for the values
		$this->children[] = $item;
	}
}