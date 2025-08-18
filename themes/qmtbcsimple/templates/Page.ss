<!DOCTYPE html>
<!--
>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
Simple. by Sara (saratusar.com, @saratusar) for Innovatif - an awesome Slovenia-based digital agency (innovatif.com/en)
Change it, enhance it and most importantly enjoy it!
>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
-->

<!--[if !IE]><!-->
<html lang="$ContentLocale">
<!--<![endif]-->
<!--[if IE 6 ]><html lang="$ContentLocale" class="ie ie6"><![endif]-->
<!--[if IE 7 ]><html lang="$ContentLocale" class="ie ie7"><![endif]-->
<!--[if IE 8 ]><html lang="$ContentLocale" class="ie ie8"><![endif]-->
<head>
	<% base_tag %>
	<title><% if $MetaTitle %>$MetaTitle<% else %>$Title<% end_if %> &raquo; $SiteConfig.Title</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	$MetaTags(false)
	<!--[if lt IE 9]>
	<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
	<style>
		@font-face {
		    font-family: 'DIN Neuzeit Grotesk LT W01 BdCn';
		    src: url("$themedResourceURL('webfonts/bb2e1211dfd31103079dbce7c49e1d4e.eot')"); /* IE9*/
		    src: url("$themedResourceURL('webfonts/bb2e1211dfd31103079dbce7c49e1d4e.eot?#iefix')") format("embedded-opentype"), /* IE6-IE8 */
		    url("$themedResourceURL('webfonts/bb2e1211dfd31103079dbce7c49e1d4e.woff2')") format("woff2"), /* chrome、firefox */
		    url("$themedResourceURL('webfonts/bb2e1211dfd31103079dbce7c49e1d4e.woff')") format("woff"), /* chrome、firefox */
		    url("$themedResourceURL('webfonts/bb2e1211dfd31103079dbce7c49e1d4e.ttf')") format("truetype"), /* chrome、firefox、opera、Safari, Android, iOS 4.2+*/
		    url("$themedResourceURL('webfonts/bb2e1211dfd31103079dbce7c49e1d4e.svg#DIN Neuzeit Grotesk LT W01 BdCn')") format("svg"); /* iOS 4.1- */
		  }
	</style>
	<link rel="shortcut icon" href="themes/simple/images/favicon.ico" />
</head>
<body class="$ClassName.ShortName<% if not $Menu(2) %> no-sidebar<% end_if %>" <% if $i18nScriptDirection %>dir="$i18nScriptDirection"<% end_if %>>
<% include Header %>
<div class="main" role="main">
	<div class="inner typography line">
		$Layout
	</div>
</div>
<% include Footer %>


</body>
</html>
