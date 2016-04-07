<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

/// E-mail outro
$string = $this->translate( 'client', 'If you have any questions, please reply to this e-mail' );

?>
<?php $this->block()->start( 'email/common/html/outro' ); ?>
<p class="email-common-outro content-block">
<?php echo $enc->html( nl2br( $string ), $enc::TRUST ); ?>
<?php echo $this->get( 'outroBody' ); ?>
</p>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/common/html/outro' ); ?>
