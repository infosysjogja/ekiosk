<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="description" content="">
  <meta name="author" content="">
  <script>
	var base_url = '<?php echo base_url(); ?>';
	var site_url = '<?php echo site_url(); ?>';
  </script>
  <title>{_title_}</title>
  {_headers_}
  </head>
  <body class="site-menubar-fold site-menubar-keep">
  <div id="page-loading"></div>
   <div id="rms-wizard" class="rms-wizard">
		<div class="rms-container">
			{_header_}
			{_content_}
		</div>
    </div>
	{_footers_}
  </body>
  <script>$(window).load(function(){$('#page-loading').fadeOut();});</script>
</html>
