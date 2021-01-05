<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager;


/**
 * MySQL index index manager for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class PgSQL
	extends \Aimeos\MShop\Index\Manager\Standard
	implements \Aimeos\MShop\Index\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $subManagers;


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'index', $manager, $name ?: 'PgSQL' );
	}


	/**
	 * Returns the list of sub-managers available for the index attribute manager.
	 *
	 * @return \Aimeos\MShop\Index\Manager\Iface[] Associative list of the sub-domain as key and the manager object as value
	 */
	protected function getSubManagers() : array
	{
		if( $this->subManagers === null )
		{
			$this->subManagers = [];
			$config = $this->getContext()->getConfig();

			foreach( $config->get( 'mshop/index/manager/submanagers', [] ) as $domain )
			{
				$name = $config->get( 'mshop/index/manager/' . $domain . '/name' );
				$this->subManagers[$domain] = $this->getObject()->getSubManager( $domain, $name ?: 'PgSQL' );
			}

			return $this->subManagers;
		}

		return $this->subManagers;
	}
}
