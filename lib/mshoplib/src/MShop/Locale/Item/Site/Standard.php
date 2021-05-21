<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	use \Aimeos\MShop\Common\Item\Config\Traits;


	private $children;


	/**
	 * Initializes the site object.
	 *
	 * @param array $values Associative list of item key/value pairs
	 * @param \Aimeos\MW\Tree\Node\Iface[] $children List of tree nodes
	 */
	public function __construct( array $values = [], array $children = [] )
	{
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Locale\Item\Site\Iface::class, $children );

		parent::__construct( 'locale.site.', $values );
		$this->children = $children;
	}


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		foreach( $this->children as $key => $item ) {
			$this->children[$key] = clone $item;
		}
	}


	/**
	 * Returns the ID of the site.
	 *
	 * @return string Unique ID of the site
	 */
	public function getSiteId() : string
	{
		return $this->get( 'locale.site.siteid', '' );
	}


	/**
	 * Sets the ID of the site.
	 *
	 * @param string $value Unique ID of the site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Locale\Item\Site\Iface
	{
		return $this->set( 'locale.site.siteid', $value );
	}


	/**
	 * Returns the code of the site.
	 *
	 * @return string Returns the code of the item
	 */
	public function getCode() : string
	{
		return $this->get( 'locale.site.code', '' );
	}


	/**
	 * Sets the code of the site.
	 *
	 * @param string $code The code to set
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Common\Item\Tree\Iface
	{
		return $this->set( 'locale.site.code', $this->checkCode( $code, 255 ) );
	}


	/**
	 * Returns the config property of the site.
	 *
	 * @return array Returns the config of the Site
	 */
	public function getConfig() : array
	{
		return $this->get( 'locale.site.config', [] );
	}


	/**
	 * Sets the config property of the site.
	 *
	 * @param array $options Options to be set for the Site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setConfig( array $options ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'locale.site.config', $options );
	}


	/**
	 * Returns the label property of the site.
	 *
	 * @return string Returns the label of the Site
	 */
	public function getLabel() : string
	{
		return $this->get( 'locale.site.label', '' );
	}


	/**
	 * Sets the label property of the site.
	 *
	 * @param string $label The label of the Site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Common\Item\Tree\Iface
	{
		return $this->set( 'locale.site.label', $label );
	}


	/**
	 * Returns the level of the item in the tree
	 *
	 * @return int Level of the item starting with "0" for the root node
	 */
	public function getLevel() : int
	{
		return 0;
	}


	/**
	 * Returns the logo path of the site.
	 *
	 * @param bool $large Return the largest image instead of the smallest
	 * @return string Returns the logo of the site
	 */
	public function getLogo( bool $large = false ) : string
	{
		if( ( $list = (array) $this->get( 'locale.site.logo', [] ) ) !== [] ) {
			return (string) ( $large ? end( $list ) : current( $list ) );
		}

		return '';
	}


	/**
	 * Returns the logo path of the site.
	 *
	 * @return string Returns the logo of the site
	 */
	public function getLogos() : array
	{
		return (array) $this->get( 'locale.site.logo', [] );
	}


	/**
	 * Returns the icon path of the site.
	 *
	 * @return string Returns the icon of the site
	 */
	public function getIcon() : string
	{
		return $this->get( 'locale.site.icon', '' );
	}


	/**
	 * Sets the icon path of the site.
	 *
	 * @param string $value The icon of the site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setIcon( string $value ) : \Aimeos\MShop\Common\Item\Tree\Iface
	{
		return $this->set( 'locale.site.icon', $value );
	}


	/**
	 * Sets the logo path of the site.
	 *
	 * @param string $value The logo of the site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setLogo( string $value ) : \Aimeos\MShop\Common\Item\Tree\Iface
	{
		return $this->set( 'locale.site.logo', [1 => $value] );
	}


	/**
	 * Sets the logo path of the site.
	 *
	 * @param array $value List of logo URLs with widths of the media file in pixels as keys
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setLogos( array $value ) : \Aimeos\MShop\Common\Item\Tree\Iface
	{
		return $this->set( 'locale.site.logo', $value );
	}


	/**
	 * Returns the ID of the parent site
	 *
	 * @return string Unique ID of the parent site
	 */
	public function getParentId() : string
	{
		return '0';
	}


	/**
	 * Returns the status property of the Site.
	 *
	 * @return int Returns the status of the Site
	 */
	public function getStatus() : int
	{
		return $this->get( 'locale.site.status', 1 );
	}


	/**
	 * Sets status property.
	 *
	 * @param int $status The status of the Site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'locale.site.status', $status );
	}


	/**
	 * Returns the supplier ID related to the site.
	 *
	 * @return string Returns the supplier ID related to the site
	 */
	public function getSupplierId() : string
	{
		return $this->get( 'locale.site.supplierid', '' );
	}


	/**
	 * Sets the supplier ID related to the site.
	 *
	 * @param string $value The supplier ID related to the site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setSupplierId( string $value ) : \Aimeos\MShop\Common\Item\Tree\Iface
	{
		return $this->set( 'locale.site.supplierid', $value );
	}

	/**
	 * Returns the theme name for the site.
	 *
	 * @return string Returns the theme name for the site or emtpy for default theme
	 */
	public function getTheme() : string
	{
		return $this->get( 'locale.site.theme', '' );
	}


	/**
	 * Sets the theme name for the site.
	 *
	 * @param string $value The theme name for the site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setTheme( string $value ) : \Aimeos\MShop\Common\Item\Tree\Iface
	{
		return $this->set( 'locale.site.theme', $value );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'locale/site';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Site item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'locale.site.code': $item = $item->setCode( $value ); break;
				case 'locale.site.label': $item = $item->setLabel( $value ); break;
				case 'locale.site.status': $item = $item->setStatus( (int) $value ); break;
				case 'locale.site.config': $item = $item->setConfig( (array) $value ); break;
				case 'locale.site.supplierid': $item = $item->setSupplierId( $value ); break;
				case 'locale.site.logo': $item = $item->setLogos( (array) $value ); break;
				case 'locale.site.theme': $item = $item->setTheme( $value ); break;
				case 'locale.site.icon': $item = $item->setIcon( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['locale.site.code'] = $this->getCode();
		$list['locale.site.icon'] = $this->getIcon();
		$list['locale.site.logo'] = $this->getLogos();
		$list['locale.site.theme'] = $this->getTheme();
		$list['locale.site.label'] = $this->getLabel();
		$list['locale.site.status'] = $this->getStatus();
		$list['locale.site.supplierid'] = $this->getSupplierId();
		$list['locale.site.hasChildren'] = $this->hasChildren();

		if( $private === true )
		{
			$list['locale.site.level'] = $this->getLevel();
			$list['locale.site.parentid'] = $this->getParentId();
			$list['locale.site.config'] = $this->getConfig();
		}

		return $list;
	}


	/**
	 * Adds a child node to this node.
	 *
	 * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to add
	 * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
	 */
	public function addChild( \Aimeos\MShop\Common\Item\Tree\Iface $item ) : \Aimeos\MShop\Common\Item\Tree\Iface
	{
		return $this;
	}


	/**
	 * Removes a child node from this node.
	 *
	 * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to remove
	 * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
	 */
	public function deleteChild( \Aimeos\MShop\Common\Item\Tree\Iface $item ) : \Aimeos\MShop\Common\Item\Tree\Iface
	{
		return $this;
	}


	/**
	 * Returns a child of this node identified by its index.
	 *
	 * @param int $index Index of child node
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Selected node
	 */
	public function getChild( int $index ) : \Aimeos\MShop\Common\Item\Tree\Iface
	{
		throw new \Aimeos\MShop\Locale\Exception( sprintf( 'Child node with index "%1$d" not available', $index ) );
	}


	/**
	 * Returns all children of this node.
	 *
	 * @return \Aimeos\Map Numerically indexed list of items implementing \Aimeos\MShop\Locale\Item\Site\Iface
	 */
	public function getChildren() : \Aimeos\Map
	{
		return map();
	}


	/**
	 * Returns the deleted children.
	 *
	 * @return \Aimeos\Map List of removed children implementing \Aimeos\MShop\Locale\Item\Site\Iface
	 */
	public function getChildrenDeleted() : \Aimeos\Map
	{
		return map();
	}


	/**
	 * Tests if a node has children.
	 *
	 * @return bool True if node has children, false if not
	 */
	public function hasChildren() : bool
	{
		return false;
	}


	/**
	 * Returns the node and its children as list
	 *
	 * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Locale\Item\Site\Iface
	 */
	public function toList() : \Aimeos\Map
	{
		return map( [$this->getId() => $this] );
	}
}
