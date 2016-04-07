<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'email/account/text/detail' ); ?>



<?php echo strip_tags( $this->translate( 'client', 'Your account' ) ); ?>

<?php	echo $this->translate( 'client', 'Account' ); ?>: <?php	echo $this->extAccountCode; ?>

<?php	echo $this->translate( 'client', 'Password' ); ?>: <?php	echo $this->extAccountPassword; ?>
<?php echo $this->get( 'detailBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/account/text/detail' ); ?>
