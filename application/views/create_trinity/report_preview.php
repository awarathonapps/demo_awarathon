<?php
defined('BASEPATH') or exit('No direct script access allowed');
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
    <?php
        $title = ($show_ranking==1 OR $show_ranking=="1") ? 'Awarathon\'s Sales Readiness Reports' : 'Sales Readiness Report (No Ranks)';
    ?>
    <p><?= $title ?></p>
</div>
<br pagebreak="true" />

<table style="width:100%">
    <tr>
        <td>
            <div class="company-title">
                <p><?php echo strtoupper($company_name); ?> ASSESSMENT</p>
            </div>
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
                    <td style="height:30px;font-size:15px;text-align:center;"><?php echo $participant_name; ?></td>
                </tr>
                <tr>
                    <td style="height:30px;font-size:15px;">Overall Score:</td>
                    <td style="height:30px;font-size:15px;text-align:center;"> </td>
                </tr>
                <tr>
                    <td style="height:30px;font-size:15px;">Overall Rating:</td>
                    <td style="height:30px;font-size:15px;text-align:center;"></td>
                </tr>
            </table>
        </td>
        <td style="width:15%">&nbsp;</td>
    </tr>
</table>

<div>&nbsp;<br /></div>

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
                    <td style="height:30px;font-size:13px;line-height:24px;text-align:center;">63.23-54.9% Category</td>
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

<!-- <div style="width:100%;">&nbsp;</div> -->

<!-- PART A -->
<table style="width:100%">
    <tr>
        <td>
            <div class="video-title">
                <p>Part A: Best Videos vs. Your Videos</p>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <table border="1" style="width:100%">
                <?php if (isset($show_ranking) && ($show_ranking == 1 or $show_ranking == "1")) { ?>
                    <tr>
                        <td style="width:10%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">S No.</td>
                        <td style="width:45%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Best Video</td>
                        <td style="width:45%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Your Video</td>
                    </tr>
                    <?php 
                    if(isset($best_video_list) && count((array)$best_video_list) > 0){
                     foreach ($best_video_list as $bvl) { ?>
                        <tr>
                            <td style="width:10%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;"><?php echo $bvl['question_series']; ?></td>
                            <td style="width:45%;height:30px;font-size:13px;font-weight:normal;line-height:14px;text-align:center;"><a style="color: #232323;text-decoration:none;" href="<?php echo $bvl['best_vimeo_url']; ?>"><?php echo $bvl['best_vimeo_url']; ?></a></td>
                            <td style="background-color:#e4e4e4;width:45%;height:30px;font-size:13px;font-weight:normal;line-height:14px;text-align:center;"><a style="color: #232323;text-decoration:none;" href="<?php echo $bvl['your_vimeo_url']; ?>"><?php echo $bvl['your_vimeo_url']; ?></a></td>
                        </tr>
                <?php } } ?>
                <?php } else { ?>
                    <tr>
                        <td style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">S No.</td>
                        <td style="width:80%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Your Video</td>
                    </tr>
                    <?php 
                    if(isset($best_video_list) && count((array)$best_video_list) > 0){
                    foreach ($best_video_list as $bvl) { ?>
                        <tr>
                            <td style="width:20%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;"><?php echo $bvl['question_series']; ?></td>
                            <td style="background-color:#e4e4e4;width:80%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;"><a style="color: #232323;text-decoration:none;" href="<?php echo $bvl['your_vimeo_url']; ?>"><?php echo $bvl['your_vimeo_url']; ?></a></td>
                        </tr>
                    <?php } ?>
                <?php } }?>
            </table>
        </td>
    </tr>
</table>

<br pagebreak="true" />
<!-- HEADER -->
<!--<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
    <tr>
        <td style="height:10px;width:60%">
            <div class="page-title">Awarathon Sales Readiness Reports</div>
        </td>
        <td style="height:10px;width:40%;text-align:right;">
            <img src="<?= $company_logo ?>" class="page-title-image"/>
        </td>
    </tr>
</table> -->

<!-- PART B -->
<table style="width:100%">
    <tr>
        <td>
            <div class="video-title">
                <p>Part B: Question Wise Assessments</p>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <table border="1" style="width:100%">


                <tr>
                    <td style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">S No.</td>
                    <td style="width:60%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Question Wise report for participant</td>
                    <td style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Your Score</td>
                </tr>
                <?php
                $l = 1;
                foreach ($question_sentance as $q) {
                ?>
                    <tr>
                        <td style="width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo "Q" . $l; ?></td>
                        <td style="width:60%;height:30px;font-size:13px;line-height:24px;text-align:left;"><?php echo $q['question']; ?></td>
                        <td style="background-color:#e4e4e4;width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;">0.00%</td>
                    </tr>
                <?php $l++;
                } ?>
            </table>
        </td>
    </tr>
</table>

<br pagebreak="true" />
<!-- HEADER -->
<!-- <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
    <tr>
        <td style="height:10px;width:60%">
            <div class="page-title">Awarathon Sales Readiness Reports</div>
        </td>
        <td style="height:10px;width:40%;text-align:right;">
            <img src="<?= $company_logo; ?>" class="page-title-image"/>
        </td>
    </tr>
</table>  --->

<!-- PART C -->
<table style="width:100%">
    <tr>
        <td>
            <div class="video-title">
                <p>Part C: Parameter Wise Assessments</p>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <table border="1" style="width:100%">

                <tr>
                    <td style="width:70%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Parameter</td>
                    <td style="width:30%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Your Score</td>
                </tr>
                <?php foreach ($parameter_label  as $ps) {  ?>
                    <tr>
                        <td style="width:70%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo $ps; ?></td>
                        <td style="background-color:#e4e4e4;width:30%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo "0.00"; // echo $ps['your_score'];
                                                                                                                                        ?></td>
                    </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
</table>

<br pagebreak="true" />
<!-- PART C -->
<table style="width:100%">
    <tr>
        <td>
            <div class="video-title" style="text-align:center;">
                <p>Key for Interpretation of the Reports</p>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <table border="1" style="width:100%">
                <tr>
                    <td style="width:50%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Parameter</td>
                    <td style="width:50%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Explanation</td>
                </tr>
                <tr>
                    <td style="width:50%;height:30px;font-size:13px;font-weight:bold;line-height:110px;text-align:center;">Product Knowledge</td>
                    <td style="width:50%;height:30px;font-size:13px;line-height:16px;">AI Models are trained on the basis of the best answer or expected answer to a particular question. The AI Model maps the intent of what has been actually spoken by the candidate to the best answer provided. Model trained to take into account, synonyms, intent of answering, etc.
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;height:30px;font-size:13px;font-weight:bold;line-height:73px;text-align:center;">Pace of Speech</td>
                    <td style="width:50%;height:30px;font-size:13px;line-height:16px;">AI Model trained to identify no. of words spoken by person in a minute. Based on Ideal pace the deviation is mapped and and marks given accordingly.
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;height:30px;font-size:13px;font-weight:bold;line-height:60px;text-align:center;">Pitch</td>
                    <td style="width:50%;height:30px;font-size:13px;line-height:16px;">Pitch of a sound is a quality that makes it possible to identify sounds as high or low. Pitch of sounds allows ordering on a frequency-related scale.
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;height:30px;font-size:13px;font-weight:bold;line-height:90px;text-align:center;">Body Language</td>
                    <td style="width:50%;height:30px;font-size:13px;line-height:16px;">Body language is one of the most important parameters, to win the confidence of the customer. AI Model trained for recognizing relevant parameters (hand gestures, facial expression, tone, etc.) for mapping body language.
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;height:30px;font-size:13px;font-weight:bold;line-height:60px;text-align:center;">Voice Modulation</td>
                    <td style="width:50%;height:30px;font-size:13px;line-height:16px;">AI Model trained to see the way in which the candidate modulates his or her voice, to ensure that the conversation is engaging to the listener.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <p class="notes">Please note that whilst interpreting the results, the scores are not good or bad. They must always be viewed in relative and not in absolute terms. The accuracy of the results are contingent on the environment in which the simulation was played and the seriousness with which it was undertaken.</p>
        </td>
    </tr>
</table>

<br pagebreak="true" />
<!-- HEADER -->
<!-- <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
    <tr>
        <td style="height:10px;width:60%">
            <div class="page-title">Awarathon Sales Readiness Reports</div>
        </td>
        <td style="height:10px;width:40%;text-align:right;">
            <img src="<?= $company_logo ?>" class="page-title-image"/>
        </td>
    </tr>
</table> -->
<!-- PART D -->
<table style="width:100%">
    <tr>
        <td>
            <div class="video-title">
                <p>Part D: Product Knowledge Assessments</p>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="txt">Text Indicators:</div>
            <div class="txt-green">Green: Perfect</div>
            <div class="txt-yellow">Yellow: Partially said but need improvement</div>
            <div class="txt-red">Red: Missed the point</div>
        </td>
    </tr>
    <!-- <tr>
        <td style="height:30px;line-height:30px;"><div style="height:30px;line-height:30px;">&nbsp;</div></td>
    </tr> -->

    <?php
    $que = 1;
    foreach ($question_sentance as $partd) { ?>
        <tr>
            <td style="height:30px;line-height:30px;">
                <div style="height:30px;line-height:0px;">&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td style="width:100%;">
                <table style="width:100%">
                    <tr nobr="true">
                        <td style="width:100%;font-size:13px;line-height:16px;"><?php echo 'Q' . $que . ') ' . $partd['question']; ?></td>
                    </tr>
                    <?php
                    $icon = '<img src="assets/images/green-tick.png" class="icon-image"/>';
                    ?>
                    <?php if(!empty($partd['senetence_keyword'])){
                    for ($i = 0; $i < count($partd['senetence_keyword']); $i++) { 
                        if(isset($partd['senetence_keyword'][$i])){
                            foreach (explode('|', $partd['senetence_keyword'][$i]) as $p) { 
                                if(!empty(trim($p))){ ?>
                                <tr nobr="true">
                                    <td style="width:4%;"><?php echo $icon;?></td>
                                    <td style="width:96%;font-size:13px;line-height:16px;vertical-align:top;"><?php echo trim($p); ?></td>
                                </tr>
                                <?php }
                            }
                        }
                    }} ?>
                </table>
            </td>
        </tr>

    <?php $que++;} ?>
</table>