<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();

$accountTarget = $this->config( 'client/html/account/history/url/target' );
$accountController = $this->config( 'client/html/account/history/url/controller', 'account' );
$accountAction = $this->config( 'client/html/account/history/url/action', 'history' );
$accountConfig = $this->config( 'client/html/account/history/url/config', array() );

?>
<div class="account-history-order common-summary">
	<a class="modify minibutton btn-close" href="<?php echo $enc->attr( $this->url( $accountTarget, $accountController, $accountAction, array(), array(), $accountConfig ) ); ?>"><?php echo $enc->html( $this->translate( 'client', 'X' ), $enc::TRUST ); ?></a>
	<h2 class="header"><?php echo $enc->html( $this->translate( 'client', 'Order details' ), $enc::TRUST ); ?></h2>
<?php echo $this->get( 'orderBody' ); ?>
	<div class="button-group">
		<a class="standardbutton btn-close" href="<?php echo $enc->attr( $this->url( $accountTarget, $accountController, $accountAction, array(), array(), $accountConfig ) ); ?>"><?php echo $enc->html( $this->translate( 'client', 'Close' ), $enc::TRUST ); ?></a>
	</div>
</div>
