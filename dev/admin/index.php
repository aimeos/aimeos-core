<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
*/


try
{
	date_default_timezone_set('UTC');

	require_once dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'Arcavias.php';

	$mshop = new MShop();

	require_once 'Init.php';
	$init = new Init( $mshop, dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config' );

	$html = $init->getHtml( realpath($_SERVER['SCRIPT_FILENAME']), $_SERVER['SCRIPT_NAME'] );
	$site = $init->getJsonSite( ( isset( $_REQUEST['site'] ) ? $_REQUEST['site'] : 'unittest' ) );
	$jsonrpc = $init->getJsonRpcController();

	$itemSchema = $jsonrpc->getJsonItemSchemas();
	$searchSchema = $jsonrpc->getJsonSearchSchemas();
	$smd = $jsonrpc->getJsonSmd( 'jsonrpc.php' );
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

		Ext.ns('MShop.config.baseurl');
		MShop.config.baseurl.content = '../images';

	</script>
</head>
<body>
	<noscript>
		<p>You need to enable javascript!</p>
	</noscript>
</body>
</html>
