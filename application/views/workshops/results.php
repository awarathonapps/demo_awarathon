<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
$asset_url =$this->config->item('assets_url');

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
	<meta name="" />
	<meta name="keywords" content="" />
	<title>Awarathon</title>
	<link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/workshops/css/reset.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/workshops/css/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/workshops/css/media-queries.css" />
    <link rel="stylesheet" href="<?php echo $asset_url; ?>assets/workshops/css/mobile-nav.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/workshops/css/modules.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/workshops/css/progressbar.css" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
	<script>
		$(function() {
			$(".meter > span").each(function() {
				$(this)
					.data("origWidth", $(this).width())
					.width(0)
					.animate({
						width: $(this).data("origWidth")
					}, 1200);
			});
		});
	</script>
</head>
<body id="home">
	<div id="wrapper" class="startchange">
        <div class="header">
        	<div id="site-header">
				<div class="nav">
					<div class="logo"><img src="<?php echo $asset_url; ?>assets/workshops/images/content/Atom-logo.png" alt=""></div>
				</div>
 			</div>
        </div>
		<div id="projects">
		     <div class="projects-80" style="text-align: center;">
		         <h3 style="font-size:24px;margin-bottom:12px">Workshop Name : <?php echo $workshopset->workshop_name;?></h3>
                    <h3 style="margin-bottom:15px">No of Participants : <span id ='tottrain'></span></h3>
            </div>
			<div class="projects-80" id="results_list">
			</div> 
		</div>
		
 	<div class="clearfix"></div>  
   	<div class="footer"></div>  
    </div>
</body>
</html>   
    <script>
		$(document).ready(function(){  
			var scroll_start = 0;
			var startchange = $('.startchange');
			var offset = startchange.offset();
			$(document).scroll(function() { 	   
				var scroll_start = $(this).scrollTop();
				if(scroll_start > offset.top) {
					$('#site-header').css('background-color', '#000');
					$('#site-header').css('border-bottom', '1px solid #efeded');
					$('#site-header').css('padding-top', '0px');
					$('#site-header').css('padding-bottom', '0px');
				} else {
					$('#site-header').css('background-color', 'transparent');
					$('.nav a').css('color', '#fff');
					$('#site-header').css('border-bottom', 'none');
				}
			});
			function ajax_load_results(){
					$.ajax({
						type: "POST",
						data: {},
						async: false,
						url: "<?php echo $base_url; ?>workshops/results/view_results/<?php echo $company_id; ?>/<?php echo $workshop_id; ?>/<?php echo $workshop_session; ?>",
						success: function (response) {
							//$('#results_list').html(response).show();
							 var data = jQuery.parseJSON(response);
							$('#results_list').html(data['html']).show();
                            $('#tottrain').html(data['toluser']).show();
						}
					});
			}
			var interval = 1000 * 30 * 1;
			setInterval(ajax_load_results, interval);
			ajax_load_results();
		});
		
	</script>
<script type="text/javascript" src="https://pagead2.googlesyndication.com/pagead/show_ads.js"></script></div>
<script src="https://code.jquery.com/jquery-2.2.1.min.js"></script>
<script type="text/javascript" src="<?php echo $asset_url; ?>assets/workshops/js/nav-mobile.js"></script>

