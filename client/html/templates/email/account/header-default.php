<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

/// Account creation e-mail subject
$this->mail()->setSubject( $this->translate( 'client', 'Your new account' ) );

?>
<?php echo $this->get( 'accountHeader' ); ?>
