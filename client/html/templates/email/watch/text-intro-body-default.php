<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$string = $this->translate( 'client', 'One or more products you are watching have been updated.' );

?>
<?php $this->block()->start( 'email/watch/text/intro' ); ?>


<?php echo wordwrap( strip_tags( $string ) ); ?>
<?php echo $this->get( 'introBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/watch/text/intro' ); ?>
