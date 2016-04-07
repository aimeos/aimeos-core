<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

?>
<?php $this->block()->start( 'checkout/standard/order/address' ); ?>
<?php echo $this->get( 'addressBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'checkout/standard/order/address' ); ?>
