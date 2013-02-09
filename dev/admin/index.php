<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: index.php 14585 2011-12-25 14:24:20Z nsendetzky $
*/


try
{
	date_default_timezone_set('UTC');

	require_once dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'MShop.php';

	spl_autoload_register( 'MShop::autoload' );

	$mshop = new MShop();

	$includePaths = $mshop->getIncludePaths();
	$includePaths[] = get_include_path();
	set_include_path( implode( PATH_SEPARATOR, $includePaths ) );

	$absdir = realpath($_SERVER['SCRIPT_FILENAME']);
	$relpath = $_SERVER['SCRIPT_NAME'];

	while ( basename( $absdir ) === basename( $relpath ) ) {
		$absdir = dirname( $absdir );
		$relpath = dirname( $relpath );
	}

	$relpath = rtrim( $relpath, '/' );
	$abslen = strlen( $absdir );
	$ds = DIRECTORY_SEPARATOR;
	$html = '';

	foreach( $mshop->getCustomPaths( 'client/extjs' ) as $base => $paths )
	{
		$relJsbPath = substr( $base, $abslen );

		foreach( $paths as $path )
		{
			$jsbPath = $relpath . $relJsbPath . $ds . $path;
			$jsbAbsPath = $base . $ds . $path;

			if( !is_file( $jsbAbsPath ) ) {
				throw new Exception( sprintf( 'JSB2 file "%1$s" not found', $jsbAbsPath ) );
			}

			$jsb2 = new MW_Jsb2_Default( $jsbAbsPath, dirname( $jsbPath ) );
			$html .= $jsb2->getHTML( 'css' );
			$html .= $jsb2->getHTML( 'js' );
		}
	}

	$configPaths = $mshop->getConfigPaths( 'mysql' );
	$configPaths[] = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config';

	require_once 'Init.php';
	$init = new Init( $configPaths );
	$jsonrpc = $init->getJsonRpcController();

	$itemSchema = $jsonrpc->getJsonItemSchemas();
	$searchSchema = $jsonrpc->getJsonSearchSchemas();
	$smd = $jsonrpc->getJsonSmd( 'jsonrpc.php' );
	$site = $init->getJsonSite( ( isset( $_REQUEST['site'] ) ? $_REQUEST['site'] : 'unittest' ) );
}
catch( Exception $e )
{
	echo $e->getMessage();
	echo $e->getTraceAsString();
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Arcavias ExtJS Admin Interface</title>
	<?php echo $html; ?>
	<script type="text/javascript">

		Ext.ns('MShop.config');
		MShop.config.activeTab = <?php echo isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 0; ?>;
		MShop.config.urlTemplate = "index.php?&site={site}&tab={tab}";

		MShop.config.site = <?php echo $site; ?>;

		MShop.config.itemschema = <?php echo $itemSchema ?>;

		MShop.config.searchschema = <?php echo $searchSchema ?>;

		MShop.config.smd = <?php echo $smd ?>;

	</script>
</head>
<body>
	<noscript>
		<p>You need to enable javascript!</p>
	</noscript>
</body>
</html>
