<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'email/account/html/detail' ); ?>
<div class="account-detail content-block">
	<div class="header">
		<h2><?php echo $enc->html( $this->translate( 'client', 'Your account' ), $enc::TRUST ); ?></h2>
	</div>
	<div class="details">
		<ul class="attr-list">
			<li class="attr-item account-code">
				<span class="name"><?php echo $enc->html( $this->translate( 'client', 'Account' ), $enc::TRUST ); ?></span>
				<span class="value"><?php echo $enc->html( $this->extAccountCode, $enc::TRUST ); ?></span>
			</li><!--
			--><li class="attr-item account-password">
				<span class="name"><?php echo $enc->html( $this->translate( 'client', 'Password' ), $enc::TRUST ); ?></span>
				<span class="value"><?php echo $enc->html( $this->extAccountPassword, $enc::TRUST ); ?></span>
			</li>
		</ul>
	</div>
<?php echo $this->get( 'detailBody' ); ?>
</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/account/html/detail' ); ?>
