<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager;


/**
 * MySQL based index for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class MySQL
	extends \Aimeos\MShop\Index\Manager\Standard
	implements \Aimeos\MShop\Index\Manager\Iface
{
	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MW\Criteria\Iface Criteria object
	 */
	public function createSearch( $default = false )
	{
		$dbm = $this->getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		$object = new \Aimeos\MW\Criteria\MySQL( $conn );

		$dbm->release( $conn );

		if( $default === true ) {
			$object->setConditions( parent::createSearch( $default )->getConditions() );
		}

		return $object;
	}
}