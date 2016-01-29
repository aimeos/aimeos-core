<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

$enc = $this->encoder();

$target = $this->request()->getTarget();
$cntl = $this->config( 'admin/jqadm/url/search/controller', 'Jqadm' );
$action = $this->config( 'admin/jqadm/url/search/action', 'search' );
$config = $this->config( 'admin/jqadm/url/search/config', array() );

$jsonTarget = $this->config( 'controller/jsonadm/url/options/target' );
$jsonCntl = $this->config( 'controller/jsonadm/url/options/controller', 'Jsonadm' );
$jsonAction = $this->config( 'controller/jsonadm/url/options/action', 'options' );
$jsonConfig = $this->config( 'controller/jsonadm/url/options/config', array() );

$extTarget = $this->config( 'admin/extjs/url/target' );
$extCntl = $this->config( 'admin/extjs/url/controller', 'Extadm' );
$extAction = $this->config( 'admin/extjs/url/action', 'index' );
$extConfig = $this->config( 'admin/extjs/url/config', array() );

$sites = $this->get( 'pageSites', array() );
$site = $this->param( 'site' );

$extParams = array( 'site' => $site, 'lang' => $this->param( 'lang' ) );

$params = $this->get( 'pageParams', array() );
$params['id'] = $this->param( 'id', '' );

?>
<div class="aimeos" data-url="<?php echo $enc->attr( $this->url( $jsonTarget, $jsonCntl, $jsonAction, array( 'site' => $site, 'resource' => '', 'id' => '' ), array(), $jsonConfig ) ); ?>">

	<nav class="navbar navbar-full">
		<a class="navbar-brand" href="https://aimeos.org/update?type={type}&version={version}">
			<img src="https://aimeos.org/check?type={type}&version={version}" title="Aimeos update" />
		</a>

		<button class="navbar-toggler hidden-sm-up" type="button" data-toggle="collapse" data-target="#collapse-navbar">&#9776;</button>

		<div class="collapse navbar-toggleable-xs" id="collapse-navbar">
			<ul class="nav navbar-nav">
				<li class="nav-item active">
					<a class="nav-link" href="<?php echo $enc->attr( $this->url( $extTarget, $extCntl, $extAction, $extParams, array(), $extConfig ) ); ?>">
						<?php echo $enc->html( $this->translate( 'admin', 'Expert mode' ) ); ?>
					</a>
				</li>
				<li class="nav-item">

					<div class="btn-group">
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?php echo $enc->attr( $this->param( 'lang', $this->translate( 'admin', 'Language' ) ) ); ?>
						</button>
						<div class="dropdown-menu">
<?php foreach( $this->get( 'pageLanguages', array() ) as $langid ) : ?>
							<a class="dropdown-item"
								href="<?php echo $enc->attr( $this->url( $target, $cntl, $action, array( 'lang' => $langid ) + $params, array(), $config ) ); ?>">
								<?php echo $enc->html( $langid ); ?>
							</a>
<?php endforeach; ?>
						</div>
					</div>

				</li>
				<li class="nav-item">

					<div class="btn-group">
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?php echo $enc->attr( $this->value( $sites, $site, $this->translate( 'admin', 'Site' ) ) ); ?>
						</button>
						<div class="dropdown-menu">
<?php foreach( $sites as $code => $label ) : ?>
							<a class="dropdown-item"
								href="<?php echo $enc->attr( $this->url( $target, $cntl, $action, array( 'site' => $code ) + $params, array(), $config ) ); ?>">
								<?php echo $enc->html( $label ); ?>
							</a>
<?php endforeach; ?>
						</div>
					</div>

				</li>
			</ul>
		</div>

	</nav>

	<div class="container">

<?php echo $this->partial( $this->config( 'admin/jqadm/partial/error', 'common/partials/error-default.php' ), array( 'errors' => $this->get( 'errors', array() ) ) ); ?>

<?php echo $this->block()->get( 'jqadm_content' ); ?>

	</div>

<?php echo $this->partial( $this->config( 'admin/jqadm/partial/confirm', 'common/partials/confirm-default.php' ) ); ?>

</div>