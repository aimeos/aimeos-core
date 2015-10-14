<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>



<?php echo strip_tags( $this->translate( 'client/html', 'Your account' ) ); ?>

<?php	echo $this->translate( 'client/html', 'Account' ); ?>: <?php	echo $this->extAccountCode; ?>

<?php	echo $this->translate( 'client/html', 'Password' ); ?>: <?php	echo $this->extAccountPassword; ?>
<?php echo $this->get( 'detailBody' ); ?>
