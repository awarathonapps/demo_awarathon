<?php
/*
* AI Report Module
* Company: Mediaworks India
* Author : Divyesh Panchal
*/
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();

?>
<style>
    .head-image{
        width:250px;

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
        width     : 150px;
        height    : auto;
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
</style>
<!--HEADING-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <img src="<?= $company_logo ?>" class="head-image"/>
        </td>
    </tr>
</table>
<div class="page-heading">
    <p>Awarathon's Sales Readiness Reports</p>
</div>
<br pagebreak="true" />


<table style="width:100%">
<tr>
    <td>
        <div class="company-title"><p><?php echo strtoupper($company_name);?> REPORT</p></div>
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
                <td style="height:30px;font-size:15px;">Manager Name:</td>
                <td style="height:30px;font-size:15px;text-align:center;"><?php echo $manager_name;?></td>
            </tr>
            <tr>
                <td style="height:30px;font-size:15px;">No. of attempts:</td>
                <td style="height:30px;font-size:15px;text-align:center;"><?php echo isset($attempt) ? $attempt : ''; ?></td>
            </tr>
        </table>
        </td>
        <td style="width:15%">&nbsp;</td>
    </tr>
</table>

<div>&nbsp;<br/></div>

<!-- PART A -->
<table style="width:100%">
    <tr>
        <td>
            <div class="video-title"><p >Part A: Question Wise Assessments</p></div>
        </td>
    </tr>
    <tr>
        <td>
        <table border="1" style="width:100%">
            <tr>
                <td style="width:10%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">S No.</td>
                <td style="width:48%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Question Wise report for participant</td>
                <td style="width:12%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">AI Score</td>
                <td style="width:15%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Assessor Score</td>
                <td style="width:15%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Combined Score (AI + Assessor)</td>
            </tr>
            <?php foreach($questions_list as $que){ ?>
            <tr nobr="true">
                <td style="width:10%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo $que['question_series'];?></td>
                <td style="width:48%;height:30px;font-size:13px;line-height:24px;text-align:left;"><?php echo $que['question'];?></td>
                <td style="background-color:#e4e4e4;width:12%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo number_format($que['ai_score'],2,'.','').'%';?></td>
                <td style="background-color:#e4e4e4;width:15%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo $que['manual_score']; //number_format($que['manual_score'],2,'.','').'%';?></td>
                <td style="background-color:#e4e4e4;width:15%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo number_format($que['combined_score'],2,'.','').'%';?></td>
            </tr>
            <?php } ?>
            </table>
            </td>
    </tr>
</table>

<!-- PART B -->
<table style="width:100%">
    <tr>
        <td>
            <div class="video-title"><p >Part B: Manager Comments</p></div>
        </td>
    </tr>
    <tr>
        <td>
        <table border="1" style="width:100%">
            <tr>
                <td style="width:10%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">S No.</td>
                <td style="width:90%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Manager Comments</td>
            </tr>
            <?php foreach($manager_comments_list as $mcl){ ?>
            <tr>
                <td style="width:10%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo $mcl['question_series'];?></td>
                <td style="width:90%;height:30px;font-size:13px;line-height:24px;text-align:left;"><?php echo $mcl['comments'];?></td>
            </tr>
            <?php } ?>
        </table>
        </td>
    </tr>
</table>

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

<br pagebreak="true" />

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
                <td style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">AI Score</td>
                <td style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Assessor Score</td>
                <td style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Combined Score (AI + Assessor)</td>
            </tr>
            <?php foreach($parameter_score as $ps){ ?>
            <tr>
                <td style="width:40%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo ($ps['parameter_label_name']!='')?$ps['parameter_label_name']:$ps['parameter_name'];?></td>
                <td style="background-color:#e4e4e4;width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo number_format($ps['your_score'],2,'.','').'%';?></td>
                <td style="background-color:#e4e4e4;width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo $ps['manual_score']; //number_format($ps['manual_score'],2,'.','').'%';?></td>
                <td style="background-color:#e4e4e4;width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo number_format($ps['combined_score'],2,'.','').'%';?></td>
            </tr>
            <?php } ?>
        </table>
        </td>
    </tr>
</table>


