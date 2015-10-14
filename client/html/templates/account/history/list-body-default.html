<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/** client/html/account/history/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 * 
 * @param string Destination of the URL
 * @since 2014.03
 * @category Developer
 * @see client/html/account/history/url/controller
 * @see client/html/account/history/url/action
 * @see client/html/account/history/url/config
 */
$accountTarget = $this->config( 'client/html/account/history/url/target' );

/** client/html/account/history/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 * 
 * @param string Name of the controller
 * @since 2014.03
 * @category Developer
 * @see client/html/account/history/url/target
 * @see client/html/account/history/url/action
 * @see client/html/account/history/url/config
 */
$accountController = $this->config( 'client/html/account/history/url/controller', 'account' );

/** client/html/account/history/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 * 
 * @param string Name of the action
 * @since 2014.03
 * @category Developer
 * @see client/html/account/history/url/target
 * @see client/html/account/history/url/controller
 * @see client/html/account/history/url/config
 */
$accountAction = $this->config( 'client/html/account/history/url/action', 'history' );

/** client/html/account/history/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 * 
 * @param string Associative list of configuration options
 * @since 2014.03
 * @category Developer
 * @see client/html/account/history/url/target
 * @see client/html/account/history/url/controller
 * @see client/html/account/history/url/action
 * @see client/html/url/config
 */
$accountConfig = $this->config( 'client/html/account/history/url/config', array() );

$orderItems = $this->get( 'listsOrderItems', array() );

$dateformat = $this->translate( 'client/html', 'Y-m-d' );
$attrformat = $this->translate( 'client/html', '%1$s at %2$s' );

$enc = $this->encoder();

?>
<?php if( !empty( $orderItems ) ) : ?>
<div class="account-history-list">
	<h1 class="header"><?php echo $enc->html( $this->translate( 'client/html', 'Order history' ), $enc::TRUST ); ?></h1>
<?php	if( empty( $orderItems ) === false ) : ?>
	<ul class="history-list">
<?php		foreach( $orderItems as $id => $orderItem ) : ?>
		<li class="history-item">
			<a href="<?php echo $enc->attr( $this->url( $accountTarget, $accountController, $accountAction, array( 'his_action' => 'order', 'his_id' => $id ), array(), $accountConfig ) ); ?>">
				<ul class="attr-list">
					<li class="attr-item order-basic">
						<span class="name"><?php echo $enc->html( $this->translate( 'client/html', 'Order ID' ), $enc::TRUST ); ?></span>
						<span class="value"><?php echo $enc->html( sprintf( $this->translate( 'client/html', '%1$s at %2$s' ), $id, date_create( $orderItem->getTimeCreated() )->format( $dateformat ) ), $enc::TRUST ); ?></span>
					</li><!--
					--><li class="attr-item order-channel">
						<span class="name"><?php echo $enc->html( $this->translate( 'client/html', 'Order channel' ), $enc::TRUST ); ?></span>
						<span class="value"><?php $code = 'order:' . $orderItem->getType(); echo $enc->html( $this->translate( 'client/html/code', $code ), $enc::TRUST ); ?></span>
					</li><!--
					--><li class="attr-item order-payment">
<?php			$code = 'pay:' . $orderItem->getPaymentStatus(); $paystatus = $this->translate( 'client/html/code', $code ); ?>
						<span class="name"><?php echo $enc->html( $this->translate( 'client/html', 'Payment' ), $enc::TRUST ); ?></span>
						<span class="value"><?php echo ( ( $date = $orderItem->getDatePayment() ) !== null ? $enc->html( sprintf( $attrformat, $paystatus, date_create( $date )->format( $dateformat ) ), $enc::TRUST ) : '' ); ?></span>
					</li><!--
					--><li class="attr-item order-delivery">
<?php			$code = 'stat:' . $orderItem->getDeliveryStatus(); $status = $this->translate( 'client/html/code', $code ); ?>
						<span class="name"><?php echo $enc->html( $this->translate( 'client/html', 'Delivery' ), $enc::TRUST ); ?></span>
						<span class="value"><?php echo ( ( $date = $orderItem->getDateDelivery() ) !== null ? $enc->html( sprintf( $attrformat, $status, date_create( $date )->format( $dateformat ) ), $enc::TRUST ) : '' ); ?></span>
					</li>
				</ul>
			</a>
		</li>
<?php		endforeach; ?>
	</ul>
<?php	endif; ?>
</div>
<?php endif; ?>
<?php echo $this->get( 'listsBody' ); ?>
