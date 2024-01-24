<?php

defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();

?>
<style>
    .head-image{
        width:200px;
        text-align: center;
        height:auto;
    }
    .page-heading{
        text-align     : center;
        width          : 100%;
        height         : 100%;
    }
    .page-heading p{
        font-size      : 30px;
        font-weight    : bold;
        text-decoration: underline;
        vertical-align : middle;
        line-height    : 500%;
    }
    .page-title{
        text-align : top;
        font-size  : 14px;
        font-weight: normal;
    }
    .page-title-image{
        text-align: top;
        width     : 90px;
        height    : 30;
        margin    : 0px auto;
    }
    .company-title{
        margin     : 5px auto; 
        width      : 50%;
        text-align : center;
        font-size  : 15px;
        font-weight: bold;
    }
    .video-title{
        width      : 100%;
        text-align : left;
        font-size  : 15px;
        font-weight: bold;
        line-height: 10px;
    }
    .notes{
        font-size  : 12px;
        line-height: 16px;
        font-weight: normal;
    }
    .txt{
        font-size  : 14px;
        line-height: 10px;
        font-weight: bold;
    }
    .txt-green{
        font-size  : 13px;
        line-height: 10px;
        font-weight: bold;
        color      : #295a32;
    }
    .txt-yellow{
        font-size  : 13px;
        line-height: 10px;
        font-weight: bold;
        color      : #d4ac5d;
    }
    .txt-red{
        font-size  : 13px;
        line-height: 10px;
        font-weight: bold;
        color      : #ff0000;
    }
    .icon-image{
        height:16px;
        width:16px;
    }
    .footer {
    position: fixed;
    bottom: 0;
	width:100%;
	background-color:#fff;
}
.footer p {
	color:#000;
	font-weight:400;
}

</style>
<!--HEADING-->
<div class="page-heading">
    <p>Sales Readiness Reports</p>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:100px;">
    <tr>
        <td style="text-align:center;">
            <img src="<?= $company_logo ?>" class="head-image"/>
        </td>
    </tr>
</table>

<br pagebreak="true" />

<!-- HEADER -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
    <tr>
        <td style="height:10px;width:60%">
            <div class="page-title">Sales Readiness Reports</div>
        </td>
        <td style="height:10px;width:40%;text-align:right;">
            <img src="<?= $company_logo ?>" class="page-title-image"/>
        </td>
    </tr>
</table>

<table style="width:100%">
<tr>
    <td>
        <div class="company-title"><p><?php echo strtoupper($company_name);?> ASSESSMENT</p></div>
    </td>
</tr>
</table>
<!-- USER DETAILS -->
<table style="width:100%">
    <tr>
        <td style="width:15%">&nbsp;</td>
        <td style="width:70%">
        <table border="1" style="width:100%">
            <tr>
                <td style="height:30px;font-size:15px;">Participant name:</td>
                <td style="height:30px;font-size:15px;text-align:center;"><?php echo $participant_name;?></td>
            </tr>
            <tr>
                <td style="height:30px;font-size:15px;">Overall Score:</td>
                <td style="height:30px;font-size:15px;text-align:center;"><?php echo number_format($overall_score,2,'.','').'%';?></td>
            </tr>
            <tr>
                <td style="height:30px;font-size:15px;">Rank:</td>
                <td style="height:30px;font-size:15px;text-align:center;"><?php echo $your_rank;?></td>
            </tr>
            <tr>
                <td style="height:30px;font-size:15px;">Overall Rating:</td>
                <td style="height:30px;font-size:15px;text-align:center;"><?php echo $rating;?></td>
            </tr>
            <tr>
                <td style="height:30px;font-size:15px;">Manager Name:</td>
                <td style="height:30px;font-size:15px;text-align:center;"><?php echo $manager_name;?></td>
            </tr>
        </table>
        </td>
        <td style="width:15%">&nbsp;</td>
    </tr>
</table>

<div>&nbsp;<br/></div>

<!-- PERCENTAGE/RATING -->
<table style="width:100%">
    <tr>
        <td>
        <table border="1" style="width:100%">
            <tr>
                <td style="background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Percentage</td>
                <td style="background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Ratings</td>
            </tr>
            <tr>
                <td style="height:30px;font-size:13px;line-height:24px;text-align:center;">Above 69.9%</td>
                <td style="height:30px;font-size:13px;line-height:24px;text-align:center;">Category A: Rockstar</td>
            </tr>
            <tr>
                <td style="height:30px;font-size:13px;line-height:24px;text-align:center;">69.9-63.23%</td>
                <td style="height:30px;font-size:13px;line-height:24px;text-align:center;">Category B: High</td>
            </tr>
            <tr>
                <td style="height:30px;font-size:13px;line-height:24px;text-align:center;">63.23-54.9% </td>
                <td style="height:30px;font-size:13px;line-height:24px;text-align:center;">Category C: Moderate</td>
            </tr>
            <tr>
                <td style="height:30px;font-size:13px;line-height:24px;text-align:center;">Below 54.9%</td>
                <td style="height:30px;font-size:13px;line-height:24px;text-align:center;">Category D: Low</td>
            </tr>
        </table>
        </td>
    </tr>
</table>
<!-- PART A -->
<table style="width:100%">
    <tr>
        <td>
            <div class="video-title"><p >Part A: Best Videos vs. Your Videos</p></div>
        </td>
    </tr>
    <tr>
        <td>
            <table border="1" style="width:100%">
                <tr>
                    <td style="width:10%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">S No.</td>
                    <td style="width:45%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Best Video</td>
                    <td style="width:45%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Your Video</td>
                </tr>
                <?php foreach($best_video_list as $bvl){ ?>
                <tr>
                    <td style="width:10%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;"><?php echo $bvl['question_series'];?></td>
                    <td style="width:45%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;"><a style="color: #232323;text-decoration:none;" href="<?php echo $bvl['best_vimeo_url'];?>"><?php echo $bvl['best_vimeo_url'];?></a></td>
                    <td style="background-color:#e4e4e4;width:45%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;"><a style="color: #232323;text-decoration:none;" href="<?php echo $bvl['your_vimeo_url'];?>"><?php echo $bvl['your_vimeo_url'];?></a></td>
                </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
</table>



<br pagebreak="true" />
<!-- HEADER -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
    <tr>
        <td style="height:10px;width:60%">
            <div class="page-title">Sales Readiness Reports</div>
        </td>
        <td style="height:10px;width:40%;text-align:right;">
            <img src="<?= $company_logo ?>" class="page-title-image"/>
        </td>
    </tr>
</table>

<!-- PART B -->
<table style="width:100%">
    <tr>
        <td>
            <div class="video-title"><p >Part B: Question Wise Assessments</p></div>
        </td>
    </tr>
    <tr>
        <td>
        <table border="1" style="width:100%">
            <tr>
                <td style="width:10%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">S No.</td>
                <td style="width:48%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Question Wise report for participant</td>
                <td style="width:12%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Your Score</td>
                <td style="width:15%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Highest Score</td>
                <td style="width:15%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Lowest Score</td>
            </tr>
            <?php foreach($questions_list as $que){ ?>
            <tr>
                <td style="width:10%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo $que['question_series'];?></td>
                <td style="width:48%;height:30px;font-size:13px;line-height:24px;text-align:left;"><?php echo $que['question'];?></td>
                <td style="background-color:#e4e4e4;width:12%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo number_format($que['your_score'],2,'.','').'%';?></td>
                <td style="background-color:#e4e4e4;width:15%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo number_format($que['highest_score'],2,'.','').'%';?></td>
                <td style="background-color:#e4e4e4;width:15%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo number_format($que['lowest_score'],2,'.','').'%';?></td>
            </tr>
            <?php } ?>
            </table>
            </td>
    </tr>
</table>
<!-- PART C -->
<table style="width:100%">
    <tr>
        <td>
            <div class="video-title"><p >Part C: Parameter Wise Assessments</p></div>
        </td>
    </tr>
    <tr>
        <td>
        <table border="1" style="width:100%">
            <tr>
                <td style="width:40%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Parameter</td>
                <td style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Your Score</td>
                <td style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Highest Score</td>
                <td style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Lowest Score</td>
            </tr>
            <?php foreach($parameter_score as $ps){ ?>
            <tr>
                <td style="width:40%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo ($ps['parameter_label_name']!='')?$ps['parameter_label_name']:$ps['parameter_name'];?></td>
                <td style="background-color:#e4e4e4;width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo number_format($ps['your_score'],2,'.','').'%';?></td>
                <td style="background-color:#e4e4e4;width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo number_format($ps['highest_score'],2,'.','').'%';?></td>
                <td style="background-color:#e4e4e4;width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo number_format($ps['lowest_score'],2,'.','').'%';?></td>
            </tr>
            <?php } ?>
        </table>
        </td>
    </tr>
</table>

<br pagebreak="true" />
<!-- HEADER -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
    <tr>
        <td style="height:10px;width:60%">
            <div class="page-title">Sales Readiness Reports</div>
        </td>
        <td style="height:10px;width:40%;text-align:right;">
            <img src="<?= $company_logo ?>" class="page-title-image"/>
        </td>
    </tr>
</table>

<!-- PART D -->
<table style="width:100%">
    <tr>
        <td>
            <div class="video-title"><p >Part D: Manager Comments</p></div>
        </td>
    </tr>
    <tr>
        <td>
        <table border="1" style="width:100%">
            <tr>
            <td style="width:10%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">S No.</td>
                <td style="width:90%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Manager Comments</td>
                </tr>
            <?php foreach($manager_comments_list as $mst){ ?>
            <tr>
            <td style="width:10%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo $mst['question_series'];?></td>
                <td style="width:90%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo $mst['comments'];?></td>
            </tr>
            <?php } ?>
        </table>
        </td>
    </tr>
</table>
<div>&nbsp;<br/></div>

<!-- OVERALL COMMENTS -->

<table style="width:100%">
    <tr>
        <td>
            <div class="video-title"><p >&nbsp;</p></div>
        </td>
    </tr>
    <tr>
        <td>
            <table border="1" style="width:100%">
                <tr>
                    <td style="width:100%;height:30px;font-size:18px;font-weight:bold;line-height:30px;text-align:center;">Overall Comments</td>
                </tr>
                <tr>
                    <td style="width:100%;height:100px;font-size:13px;line-height:16px;"><?php echo $overall_comments;?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>


