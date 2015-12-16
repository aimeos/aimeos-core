<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>



<?php echo strip_tags( $this->translate( 'client', 'Your account' ) ); ?>

<?php	echo $this->translate( 'client', 'Account' ); ?>: <?php	echo $this->extAccountCode; ?>

<?php	echo $this->translate( 'client', 'Password' ); ?>: <?php	echo $this->extAccountPassword; ?>
<?php echo $this->get( 'detailBody' ); ?>
