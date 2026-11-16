<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\MShop\Locale;


/**
 * Sets the policy and the users for testing the site tree restrictions
 */
trait SiteUserTrait
{
	protected function allowAll() : void
	{
		$this->context->setPolicy( new class implements \Aimeos\MShop\PolicyIface {
			public function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : ?bool {
				return true;
			}
		} );
	}


	protected function super() : void
	{
		$view = $this->context->view();
		$view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\All( $view ) );
		$this->context->setView( $view );
	}


	protected function user( string $siteid ) : void
	{
		$view = $this->context->view();
		$view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\Standard( $view, ['admin'] ) );

		$user = \Aimeos\MShop::create( $this->context, 'customer' )->create( ['customer.siteid' => $siteid] );
		$this->context->setView( $view )->setUser( $user );
	}
}
