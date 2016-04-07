<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$string = $this->translate( 'client', 'An account has been created for you.' );

?>
<?php $this->block()->start( 'email/account/text/intro' ); ?>


<?php echo wordwrap( strip_tags( $string ) ); ?>
<?php echo $this->get( 'introBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/account/text/intro' ); ?>
