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

        <link rel = "stylesheet" href = "http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
    
    <!---WEBSITE CSS--->
	<link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/workshops/css/reset.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/workshops/css/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/workshops/css/media-queries.css" />
    <!---WEBSITE CSS--->
    
    <!---MOBILE NAVIGATION CSS--->
    <link rel="stylesheet" href="<?php echo $asset_url; ?>assets/workshops/css/mobile-nav.css"/>
    <!---MOBILE NAVIGATION CSS--->
    
    <!---NAVIGATION CSS--->
    <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/workshops/css/modules.min.css" />
    <!---NAVIGATION CSS--->
    
    <!---PROGRESS BAR CSS--->
	<link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/workshops/css/progressbar.css" />
    <!---PROGRESS BAR CSS--->
    <!---progress bar script start--->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
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
    <!---progress bar script end--->

        
</head>



<body id="home">
	<div id="wrapper" class="startchange">
       
		<!------START FULL DIV HEADER-------> 
        <div class="header">
        	<div id="site-header">
            
            
            <!----------Main Navigation START ----------->
            <div class="nav">
            	<div class="logo"><img src="<?php echo $asset_url; ?>assets/workshops/images/content/Atom-logo.png" alt=""></div>
           </div>
                
 			</div><!---close div site-header--->
        </div><!---close div header--->
   		<!------CLOSE FULL DIV HEADER-------> 
 
 
   	<div id="projects">
           
   		<div class="projects-80">
        	<a href="#" class="white-space-link">
             <div class="project-one-fourth pro-mar-right">            	
                <img src="<?php echo $asset_url; ?>assets/workshops/images/content/awar-logo.jpg" alt="">
                <h4>
                Supersize Card. Order a Regular Jar and receive a Larger Serving instead.</h4>
                <h5><span>by</span> The White Owl Brewery & Bistro</h5>
                <h6>The Supersize Card must be used within one month from the date of receipt of the card.</h6>
                <div class="frnd90">
            		<div class="user70">Raised 
                    	<i class="fa fa-inr"></i>
                        2,85,915
                        </div>
                		<div class="stride30">55%</div>
                			<div class="clearfix"></div>
                            <div class="meter orange nostripes">
                                <span style="width:60%"></span>
                            </div>
            	</div><!--Close DIV frnd90-->
                <div class="clearfix"></div>
                <div class="number">
                	<div class="col-3 pad-bdr">
                    	<i class="fa fa-inr" style="float:left; font-size:17px; margin:3px 5px 0 0;"></i>
                        <div class="value">25</div>
                        <div style="width:100%;">Amount</div>
                    </div>
                    <div class="col-3 pad-bdr">
                    	<i class="fa fa-ticket" style="float:left; font-size:17px; margin:3px 5px 0 0;"></i>
                        <div class="value">168</div>
                        <span>Voucher Left</span>
                    </div>
                    <div class="col-3">
                    	<i class="fa fa-lg fa-clock-o" style="float:left; margin:5px 5px 0 0;"></i>
                        <div class="value">17</div>
                        <span>Days left</span>
                    </div>
                </div><!--Close DIV number-->  
                </a>
                <a href="#">   
                <div class="clickbt">
                	View Results
                </div> 
                </a>          
            </div><!--Close DIV project-one-fourth pro-mar-right-->
            
            <!-----1st div end here----->
            
            
                        
           <div class="project-one-fourth pro-mar-right">
            	<a href="#" class="white-space-link">
                <img src="<?php echo $asset_url; ?>assets/workshops/images/content/awar-logo.jpg" alt="">
                <h4>Free Cappuccino</h4>
                <h5><span>by</span> The Pantry</h5>
                <h6>The Supersize Card must be used within one month from the date of receipt of the card.</h6>
                <div class="frnd90">
            		<div class="user70">Raised 
                    	<i class="fa fa-inr"></i>
                        2,85,915
                        </div>
                		<div class="stride30">55%</div>
                			<div class="clearfix"></div>
                            <div class="meter orange nostripes">
                                <span style="width:60%"></span>
                            </div>
            	</div><!--Close DIV frnd90-->
                <div class="clearfix"></div>
                 <div class="number">
                	<div class="col-3 pad-bdr">
                    	<i class="fa fa-inr" style="float:left; font-size:17px; margin:3px 5px 0 0;"></i>
                        <div class="value">25</div>
                        <div style="width:100%;">Amount</div>
                    </div>
                    <div class="col-3 pad-bdr">
                    	<i class="fa fa-ticket" style="float:left; font-size:17px; margin:3px 5px 0 0;"></i>
                        <div class="value">168</div>
                        <span>Voucher Left</span>
                    </div>
                    <div class="col-3">
                    	<i class="fa fa-lg fa-clock-o" style="float:left; margin:5px 5px 0 0;"></i>
                        <div class="value">17</div>
                        <span>Days left</span>
                    </div>
                </div><!--Close DIV number-->
                </a>
                <a href="#">   
                <div class="clickbt">
                	View Results
                </div> 
                </a>   
            </div><!--Close DIV project-one-fourth pro-mar-right-->
            <!-----2nd div end here----->
            
            
            
            
           <div class="project-one-fourth">
            	<a href="#" class="white-space-link">
                <img src="<?php echo $asset_url; ?>assets/workshops/images/content/awar-logo.jpg" alt="">
                <h4>Supersize Card. Order a Regular Jar and receive a Larger Serving instead.</h4>
                <h5><span>by</span> The White Owl Brewery & Bistro</h5>
                <h6>The date of receipt of the card.</h6>
                <div class="frnd90">
            		<div class="user70">Raised 
                    	<i class="fa fa-inr"></i>
                        2,85,915
                        </div>
                		<div class="stride30">55%</div>
                			<div class="clearfix"></div>
                            <div class="meter orange nostripes">
                                <span style="width:60%"></span>
                            </div>
            	</div><!--Close DIV frnd90-->
                <div class="clearfix"></div>
                 <div class="number">
                	<div class="col-3 pad-bdr">
                    	<i class="fa fa-inr" style="float:left; font-size:17px; margin:3px 5px 0 0;"></i>
                        <div class="value">25</div>
                        <div style="width:100%;">Amount</div>
                    </div>
                    <div class="col-3 pad-bdr">
                    	<i class="fa fa-ticket" style="float:left; font-size:17px; margin:3px 5px 0 0;"></i>
                        <div class="value">168</div>
                        <span>Voucher Left</span>
                    </div>
                    <div class="col-3">
                    	<i class="fa fa-lg fa-clock-o" style="float:left; margin:5px 5px 0 0;"></i>
                        <div class="value">17</div>
                        <span>Days left</span>
                    </div>
                </div><!--Close DIV number-->
                </a>
                <a href="#">   
                <div class="clickbt">
                	View Results
                </div> 
                </a>   
            </div><!--Close DIV project-one-fourth pro-mar-right-->
            <!-----3rd div end here----->
            <div class="clearfix"></div>
            
            <a href="#" class="white-space-link">
            <div class="project-one-fourth pro-mar-right pro-mar-top">
            	<img src="<?php echo $asset_url; ?>assets/workshops/images/content/awar-logo.jpg" alt="">
                <h4>Supersize Card. Order a Regular Jar and receive a Larger Serving instead.</h4>
                <h5><span>by</span> The White Owl Brewery & Bistro</h5>
                <h6>The Supersize Card must be used within one month from the date of receipt of the card. one month from the date of receipt of the card.</h6>
                <div class="frnd90">
            		<div class="user70">Raised 
                    	<i class="fa fa-inr"></i>
                        2,85,915
                        </div>
                		<div class="stride30">55%</div>
                			<div class="clearfix"></div>
                            <div class="meter orange nostripes">
                                <span style="width:60%"></span>
                            </div>
            	</div><!--Close DIV frnd90-->
                <div class="clearfix"></div>
                 <div class="number">
                	<div class="col-3 pad-bdr">
                    	<i class="fa fa-inr" style="float:left; font-size:17px; margin:3px 5px 0 0;"></i>
                        <div class="value">25</div>
                        <div style="width:100%;">Amount</div>
                    </div>
                    <div class="col-3 pad-bdr">
                    	<i class="fa fa-ticket" style="float:left; font-size:17px; margin:3px 5px 0 0;"></i>
                        <div class="value">168</div>
                        <span>Voucher Left</span>
                    </div>
                    <div class="col-3">
                    	<i class="fa fa-lg fa-clock-o" style="float:left; margin:5px 5px 0 0;"></i>
                        <div class="value">17</div>
                        <span>Days left</span>
                    </div>
                </div><!--Close DIV number-->
                </a>
                <a href="#">   
                <div class="clickbt">
                	View Results
                </div> 
                </a>   
            </div><!--Close DIV project-one-fourth pro-mar-right-->
            <!-----4th div end here----->
            
        </div><!--close div projects-80--> 
   	</div><!--close div projects--> 
 	<div class="clearfix"></div>  
   
 
   	<div class="footer"></div><!--close div footer-->     
            
    </div><!--close div wrapper-->   
</body>
</html>   

	<!--HEADER SCROLL SCRIPT JS START-->
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
	   function ajax_load_workshops(){
			$.ajax({
				type: "POST",
				data: {},
				async: false,
				url: "<?php echo $base_url; ?>workshops/index/load_workshops",
				success: function (msg) {
					
				}
			});
	   }
	   var interval = 1000 * 60 * 1;
	   setInterval(ajax_load_workshops, interval);

	});
	</script>
    <!--HEADER SCROLL SCRIPT JS END-->
    
<!--MOBILE NAVIGATION SCRIPT JS START-->   
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script></div>
<script src="http://code.jquery.com/jquery-2.2.1.min.js"></script>
<script type="text/javascript" src="<?php echo $asset_url; ?>assets/workshops/js/nav-mobile.js"></script>
<!--MOBILE NAVIGATION SCRIPT JS END-->
