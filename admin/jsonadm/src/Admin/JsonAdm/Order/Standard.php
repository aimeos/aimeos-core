<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Order;


/**
 * JSON API order client
 *
 * @package Controller
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Admin\JsonAdm\Base
	implements \Aimeos\Admin\JsonAdm\Common\Iface
{
	/** admin/jsonadm/order/decorators/excludes
	 * Excludes decorators added by the "common" option from the JSON API clients
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "admin/jsonadm/common/decorators/default" before they are wrapped
	 * around the Jsonadm client.
	 *
	 *  admin/jsonadm/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Admin\JsonAdm\Common\Decorator\*") added via
	 * "admin/jsonadm/common/decorators/default" for the JSON API client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/order/decorators/global
	 * @see admin/jsonadm/order/decorators/local
	 */

	/** admin/jsonadm/order/decorators/global
	 * Adds a list of globally available decorators only to the Jsonadm client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Controller\Jsonadm\Common\Decorator\*") around the Jsonadm
	 * client.
	 *
	 *  admin/jsonadm/order/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Controller\Jsonadm\Common\Decorator\Decorator1" only to the
	 * "order" Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/order/decorators/excludes
	 * @see admin/jsonadm/order/decorators/local
	 */

	/** admin/jsonadm/order/decorators/local
	 * Adds a list of local decorators only to the Jsonadm client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Controller\Jsonadm\Order\Decorator\*") around the Jsonadm
	 * client.
	 *
	 *  admin/jsonadm/order/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Controller\Jsonadm\Order\Decorator\Decorator2" only to the
	 * "order" Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/order/decorators/excludes
	 * @see admin/jsonadm/order/decorators/global
	 */


	/**
	 * Returns the requested resource or the resource list
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function get( $body, array &$header, &$status )
	{
		/** admin/jsonadm/partials/order/template-data
		 * Relative path to the data partial template file for the order client
		 *
		 * Partials are templates which are reused in other templates and generate
		 * reoccuring blocks filled with data from the assigned values. The data
		 * partial creates the "data" part for the JSON API response.
		 *
		 * The partial template files are usually stored in the templates/partials/ folder
		 * of the core or the extensions. The configured path to the partial file must
		 * be relative to the templates/ folder, e.g. "partials/data-standard.php".
		 *
		 * @param string Relative path to the template file
		 * @since 2016.01
		 * @category Developer
		 */
		$this->getView()->assign( array( 'partial-data' => 'admin/jsonadm/partials/order/template-data' ) );

		return parent::get( $body, $header, $status );
	}


	/**
	 * Returns the items with parent/child relationships
	 *
	 * @param array $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	protected function getChildItems( array $items, array $include )
	{
		$list = array();

		if( in_array( 'order/base', $include ) )
		{
			$ids = array();

			foreach( $items as $item ) {
				$ids[] = $item->getBaseId();
			}

			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'order/base' );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', 'order.base.id', $ids ) );

			$list = array_merge( $list, $manager->searchItems( $search ) );
		}

		if( in_array( 'order/status', $include ) )
		{
			$ids = array_keys( $items );
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'order/status' );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', 'order.status.parentid', $ids ) );

			$list = array_merge( $list, $manager->searchItems( $search ) );
		}

		return $list;
	}
}
