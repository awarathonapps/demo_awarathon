<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$asset_url =$this->config->item('assets_url');

?>
<meta charset="utf-8" />
<title>Awarathon - CMS</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta content="We provide custom-made solutions for trainers and corporates seeking to measure the impact of training.Our Products And Services like Gaming Solutions, Technology, Data & Analytics and Smart Assessments" name="description" />
<meta content="" name="author" />
<!-- BEGIN PAGE FIRST SCRIPTS -->
<script src="<?php echo $asset_url; ?>assets/global/plugins/pace/pace.min.js" type="text/javascript"></script>
<link href="<?php echo $asset_url; ?>assets/global/plugins/pace/themes/pace-theme-flash.css" rel="stylesheet" type="text/css" />
<!-- END PAGE FIRST SCRIPTS -->
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-Q66MDDDN97"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-Q66MDDDN97');
</script>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />

<link href="<?php echo $asset_url; ?>assets/layouts/layout/fonts/fonts.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
<!-- END GLOBAL MANDATORY STYLES -->

<!-- BEGIN PAGE LEVEL PLUGINS -->

<!--<link href="< ?php echo $asset_url; ?>assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css" rel="stylesheet" type="text/css" />
<link href="< ?php echo $asset_url; ?>assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet" type="text/css" />-->
<!--<link href="< ?php echo $asset_url; ?>assets/global/plugins/jcrop/css/jquery.Jcrop.min.css" rel="stylesheet" type="text/css" />-->

<link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/global/plugins/jquery-confirm/dist/jquery-confirm.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/global/plugins/ladda/ladda-themeless.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
<!--<link href="< ?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />

<link href="< ?php echo $asset_url; ?>assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<link href="< ?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<link href="< ?php echo $asset_url; ?>assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css" />-->

<!--<link href="< ?php echo $asset_url; ?>assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
<link href="< ?php echo $asset_url; ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />-->

<!-- END PAGE LEVEL PLUGINS -->


<!-- BEGIN THEME GLOBAL STYLES -->
<link href="<?php echo $asset_url; ?>assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
<!-- END THEME GLOBAL STYLES -->

<!-- BEGIN THEME LAYOUT STYLES -->
<link href="<?php echo $asset_url; ?>assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/layouts/layout/css/themes/black_orange.min.css" rel="stylesheet" type="text/css" id="style_color" />
<link href="<?php echo $asset_url; ?>assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $asset_url; ?>assets/layouts/layout/css/custom.css" rel="stylesheet" type="text/css" />
<!-- END THEME LAYOUT STYLES -->

<link rel="shortcut icon" href="<?php echo $asset_url; ?>favicon.ico" />
<style>
/*body > :not(.pace),body:before,body:after {
  -webkit-transition:opacity .2s ease-in-out;
  -moz-transition:opacity .2s ease-in-out;
  -o-transition:opacity .2s ease-in-out;
  -ms-transition:opacity .2s ease-in-out;
  transition:opacity .2s ease-in-out
}*/

body:not(.pace-done) > :not(.pace),body:not(.pace-done):before,body:not(.pace-done):after {
   opacity:0;
}
.pace-running > :not(.pace) {
   opacity:0;
}
/*.pace-done > :not(.pace) {
  opacity: 1;
  transition: opacity .5s ease;
}*/
</style>
