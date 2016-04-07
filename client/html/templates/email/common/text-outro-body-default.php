<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

/// E-mail outro
$string = $this->translate( 'client', 'If you have any questions, please reply to this e-mail' );

?>
<?php $this->block()->start( 'email/common/text/outro' ); ?>


<?php echo wordwrap( strip_tags( $string ) ); ?>
<?php echo $this->get( 'outroBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/common/text/outro' ); ?>
