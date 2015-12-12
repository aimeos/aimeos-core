<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

$map = $this->get( 'selectMap', array() );
$params = $this->get( 'selectParams', array() );
$langId = $this->get( 'selectLanguageId', 'en' );
$currencyId = $this->get( 'selectCurrencyId', 'EUR' );

/** client/html/locale/select/language/url/config
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
 * @since 2014.09
 * @category Developer
 */
$config = $this->config( 'client/html/locale/select/language/url/config', array() );

?>
<div class="locale-select-language">
	<h2 class="header"><?php echo $this->translate( 'client/html', 'Select language' ); ?></h2>
	<ul class="select-menu">
		<li class="select-dropdown select-current"><a href="#"><?php echo $this->translate( 'client/html/language', $langId ); ?></a>
			<ul class="select-dropdown">
<?php	foreach( $map as $lang => $list ) : ?>
<?php		$locParams = ( isset( $list[$currencyId] ) ? (array) $list[$currencyId] : (array) reset( $list ) ); ?>
				<li class="select-item <?php echo ( $lang === $langId ? 'active' : '' ); ?>">
					<a href="<?php echo $enc->attr( $this->url( $this->request()->getTarget(), $this->param( 'controller' ), $this->param( 'action' ), array_merge( $params, $locParams ), array(), $config ) ); ?>">
<?php		echo $enc->html( $this->translate( 'client/html/language', $lang ), $enc::TRUST ); ?>
					</a>
				</li>
<?php	endforeach; ?>
			</ul>
		</li>
	</ul>
<?php echo $this->get( 'languageBody' ); ?>
</div>
