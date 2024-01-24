<?php $base_url= base_url(); ?>
<meta charset="utf-8" />
<title>Awarathon - CMS</title>
<meta name="description" content="We provide custom-made solutions for trainers and corporates seeking to measure the impact of training.Our Products And Services like Gaming Solutions, Technology, Data & Analytics and Smart Assessments" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

<!--begin::Fonts-->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
<link href="<?= $base_url;?>/assets/layouts/auth/css/fonts.css" rel="stylesheet" type="text/css" />
<!--end::Fonts-->

<!--begin::Page Custom Styles(used by this page)-->
<link href="<?= $base_url;?>/assets/layouts/auth/css/login-1.css" rel="stylesheet" type="text/css" />
<!--end::Page Custom Styles-->

<!--begin::Global Theme Styles(used by all pages)-->
<link href="<?= $base_url;?>/assets/layouts/auth/css/plugins.bundle.css" rel="stylesheet" type="text/css" />
<link href="<?= $base_url;?>/assets/layouts/auth/js/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css" />
<link href="<?= $base_url;?>/assets/layouts/auth/css/style.bundle.css" rel="stylesheet" type="text/css" />
<!--end::Global Theme Styles-->

<!--begin::Layout Themes(used by all pages)-->
<link href="<?= $base_url;?>/assets/layouts/auth/css/base-light.css" rel="stylesheet" type="text/css" />
<link href="<?= $base_url;?>/assets/layouts/auth/css/menu-light.css" rel="stylesheet" type="text/css" />
<link href="<?= $base_url;?>/assets/layouts/auth/css/dark.css" rel="stylesheet" type="text/css" />
<link href="<?= $base_url;?>/assets/layouts/auth/css/custom.css" rel="stylesheet" type="text/css" />
<!--end::Layout Themes-->

<link rel="shortcut icon" href="<?= $base_url;?>favicon.ico" />
<?php if(isset($google_client_id)){ ?>
<meta name="google-signin-client_id" content="<?=$google_client_id; ?>">
<script src="https://apis.google.com/js/platform.js" ></script>
<?php } ?>
<style>
    .form-group {
        margin-bottom: 1rem !important;
    }
    .m-3 {
        margin: 0.30rem !important;
    }

    .mt-3,
    .my-3 {
        margin-top: 0.30rem !important;
    }

    .mr-3,
    .mx-3 {
        margin-right: 0.30rem !important;
    }

    .mb-3,
    .my-3 {
        margin-bottom: 0.30rem !important;
    }

    .ml-3,
    .mx-3 {
        margin-left: 0.30rem !important;
    }
    .lbl-password{
        float: left;
    }
    .password-help{
        float: right;
    }
</style>