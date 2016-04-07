<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

/// Payment e-mail legal information
$string = $this->translate( 'client',  'All orders are subject to our terms and conditions.' );

?>
<?php $this->block()->start( 'email/common/text/legal' ); ?>



<?php echo wordwrap( strip_tags( $string ) ); ?>
<?php echo $this->get( 'legalBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/common/text/legal' ); ?>
