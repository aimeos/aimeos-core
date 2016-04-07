<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

?>
<?php $this->block()->start( 'email/watch' ); ?>
<?php echo $this->get( 'watchBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/watch' ); ?>
