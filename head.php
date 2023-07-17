<?php require "$base_dir/vendor/autoload.php";
header("Content-Type: text/html; charset=UTF-8");
require "$base_dir/lib/functions.php";
require "$base_dir/lib/app_data.php";
$url_filename = substr($_SERVER["PHP_SELF"], strrpos($_SERVER["PHP_SELF"], "/") + 1);
$url = substr(str_replace($base_url, '', $_SERVER["REQUEST_URI"]), 1);
$url = strpos($url, '?') !== false ? substr($url, 0, strpos($url, '?')) : $url;
$url = strpos($url, '#') !== false ? substr($url, 0, strpos($url, '#')) : $url;
$query_params = http_build_query($_GET);
if ($query_params) $query_params = '?'.$query_params;
if (!empty($login_required)) require "$base_dir/lib/login.php";

ini_set("display_errors", empty($produccion));
error_reporting(!empty($produccion) ? 0 : E_ALL);
$seconds_to_cache = !empty($seconds_to_cache) ? $seconds_to_cache : 0;
$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
header("Expires: $ts");
if ($seconds_to_cache) {
	header("Pragma: public");
	header("Cache-control: max-age=$seconds_to_cache");
} else {
	header("Pragma: no-cache");
	header("Cache-Control: no-cache, must-revalidate");
} ?><!DOCTYPE html>
<html lang="<?= $i18n_idioma;?>">
<head>
	<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" id="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="canonical" href="<?= ($https ? "https" : "http")."://".$_SERVER['HTTP_HOST']."/".$url;?>" />
	<?php if (!isset($meta_title)) $meta_title = $web_title;?> 
	<title><?= $meta_title;?></title>
	<meta name="title" content="<?= $meta_title;?>" />
	<?php if (!empty($meta_desc)) { ?><meta name="description" content="<?= $meta_desc;?>" /><?php } ?> 
	<?php if (!empty($meta_keywords)) { ?><meta name="keywords" content="<?= $meta_keywords;?>" /><?php } ?> 
	
	<!-- Open Graph markup: https://developers.facebook.com/docs/sharing/webmasters#markup -->
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?= $meta_title;?>" />
	<?php if (!empty($meta_desc)) { ?><meta property="og:description" content="<?= $meta_desc;?>" /><?php } ?> 
	<meta property="og:url" content="<?= ($https ? "https" : "http")."://".$_SERVER['HTTP_HOST'].$url;?>" />
	<meta property="og:image" content="<?= ($https ? "https" : "http")."://".$_SERVER['HTTP_HOST'].$base_url.'/'.(!empty($meta_img) ? $meta_img : 'img/logo.svg');?>" />
	<meta property="og:locale" content="<?= !empty($localizacion) ? $localizacion : 'es_ES';?>" />
	<meta property="og:site_name" content="<?= $web_title;?>" />
	
	<!-- Twitter cards: https://developer.twitter.com/en/docs/twitter-for-websites/cards/overview/abouts-cards -->
	<meta name="twitter:card" content="summary">
	<meta name="twitter:title" content="<?= $meta_title;?>" />
	<?php if (!empty($meta_desc)) { ?><meta name="twitter:description" content="<?= $meta_desc;?>"><?php } ?> 
	<meta name="twitter:url" content="<?= ($https ? "https" : "http")."://".$_SERVER['HTTP_HOST'].$url;?>">
	<meta name="twitter:image" content="<?= ($https ? "https" : "http")."://".$_SERVER['HTTP_HOST'].$base_url.'/'.(!empty($meta_img) ? $meta_img : 'img/logo.svg');?>">
	
	<meta name="author" content="<?= $web_author;?>" />
	<?php if (!empty($web_geo)) { ?>
	<meta name="geo.region" content="<?= $web_geo['region'];?>" />
	<meta name="geo.placename" content="<?= $web_geo['location'];?>" />
	<meta name="geo.position" content="<?= $web_geo['lat'];?>;<?= $web_geo['lng'];?>" />
	<meta name="ICBM" content="<?= $web_geo['lat'];?>, <?= $web_geo['lng'];?>" />
	<?php } ?>
	
	<meta name="robots" content="<?= !empty($produccion) ? 'index,follow' : 'noindex,nofollow';?>" />
	<?php if (count($i18n_permitidos) > 1) {
		foreach ($i18n_permitidos as $idioma_id) {
			if ($idioma_id == $localizacion) continue;?> 
	<link rel="alternate" href="<?= ($https ? "https" : "http")."://".$_SERVER['HTTP_HOST'].$base_url.'/'.i18n_url($url, $idioma_id, $localizacion).$query_params;?>" hreflang="<?= str_replace('_', '-', strtolower($idioma_id));?>" />
		<?php }
	} ?>

	<meta name="mobile-web-app-capable" content="yes" />
	<meta name="application-name" content="<?= $meta_title;?>" />
	<link rel="icon" sizes="192x192" href="<?= $base_url;?>/img/touch-icon.png" />

	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="apple-mobile-web-app-title" content="<?= $meta_title;?>" />
	<link rel="apple-touch-icon" href="<?= $base_url;?>/img/touch-icon.png" />

	<meta name="msapplication-TileImage" content="<?= $base_url;?>/img/touch-icon.png" />
	<meta name="msapplication-TileColor" content="<?= $theme_color;?>" />
	<meta name="msapplication-tap-highlight" content="no" />
	<meta name="msapplication-square310x310logo" content="<?= $base_url;?>/img/touch-icon.png" />

	<link rel="icon" type="image/svg+xml" href="<?= $base_url;?>/img/favicon.svg">
	<link rel="icon" type="image/png" href="<?= $base_url;?>/img/favicon.png"> 
	<?php $css_libs = [
		"vendor/twbs/bootstrap/dist/css/bootstrap.min.css",
		"css/colors.css",
		"css/main.css",
		"css/style.css"
	];
	
	load_libs('css', $base_dir, $base_url, $css_libs);?>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>

	<?php if (!empty($app_recaptcha_type) && $app_recaptcha_type == 'v3') { ?>
	<script src='https://www.google.com/recaptcha/api.js?render=<?= $app_recaptcha[$app_recaptcha_type]['site_key'];?>'></script>
	<script>
	grecaptcha.ready(function() {
		grecaptcha.execute('<?= $app_recaptcha[$app_recaptcha_type]['site_key'];?>', {action: 'form_<?= str_replace('.', '', $_SERVER['HTTP_HOST']);?>'})
		.then(function(token) {
			document.querySelectorAll(".recaptchaResponse").forEach(elem => (elem.value = token))
		})
	})
	</script>
	<?php } ?>

	<!--[if lt IE 10]>
	<div class="alert alert-danger" role="alert">Esta web no soporta su versión de Internet Explorer. Por favor, actualice su navegador.</div>
	<![endif]-->
</head>
<body>
