<?php
/*
 * AI Report Module
 * Company: Mediaworks India
 * Author : Divyesh Panchal
 */
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();

?>
<style>
    .head-image {
        width: 250px;
        height: auto;
    }

    .page-heading {
        text-align: center;
        width: 100%;
        height: 100%;
    }

    .page-heading p {
        font-size: 30px;
        font-weight: bold;
        text-decoration: underline;
        vertical-align: middle;
        line-height: 500%;
    }

    .page-title {
        text-align: top;
        font-size: 14px;
        font-weight: normal;
    }

    .page-title-image {
        text-align: top;
        width: 150px;
        height: auto;
        margin: 0px auto;
    }

    .company-title {
        margin: 5px auto;
        width: 50%;
        text-align: center;
        font-size: 15px;
        font-weight: bold;
    }

    .video-title {
        width: 100%;
        text-align: left;
        font-size: 15px;
        font-weight: bold;
        line-height: 10px;
    }

    .notes {
        font-size: 12px;
        line-height: 16px;
        font-weight: normal;
    }

    .txt {
        font-size: 14px;
        line-height: 10px;
        font-weight: bold;
    }

    .txt-green {
        font-size: 13px;
        line-height: 10px;
        font-weight: bold;
        color: #295a32;
    }

    .txt-yellow {
        font-size: 13px;
        line-height: 10px;
        font-weight: bold;
        color: #d4ac5d;
    }

    .txt-red {
        font-size: 13px;
        line-height: 10px;
        font-weight: bold;
        color: #ff0000;
    }

    .icon-image {
        height: 16px;
        width: 16px;
    }
</style>
<!--HEADING-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <img src="<?= $company_logo ?>" class="head-image" />
        </td>
    </tr>
</table>
<div class="page-heading">
    <?php
    $title = ($show_ranking == 1 or $show_ranking == "1") ? 'Awarathon\'s Sales Readiness Reports' : 'Sales Readiness Report (No Ranks)';
    ?>
    <p>
        <?= $title ?>
    </p>
</div>
<br pagebreak="true" />

<table style="width:100%">
    <tr>
        <td>
            <div class="company-title">
                <p>
                    <?php echo strtoupper($company_name); ?> ASSESSMENT
                </p>
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
                    <td style="height:30px;font-size:15px;text-align:center;">
                        <?php echo $participant_name; ?>
                    </td>
                </tr>
                <tr>
                    <td style="height:30px;font-size:15px;">Overall Score:</td>
                    <td style="height:30px;font-size:15px;text-align:center;">
                        <?php echo number_format($overall_score, 2, '.', '') . '%'; ?>
                    </td>
                </tr>
                <?php if ($show_ranking == 1 or $show_ranking == "1") { ?>
                    <tr>
                        <td style="height:30px;font-size:15px;">Rank:</td>
                        <td style="height:30px;font-size:15px;text-align:center;">
                            <?php echo $your_rank; ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td style="height:30px;font-size:15px;">Overall Rating:</td>
                    <td style="height:30px;font-size:15px;text-align:center;">
                        <?php echo $rating; ?>
                    </td>
                </tr>
                <tr>
                    <td style="height:30px;font-size:15px;">No. of attempts:</td>
                    <td style="height:30px;font-size:15px;text-align:center;">
                        <?php echo isset($attempt) ? $attempt : ''; ?>
                    </td>
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
                    <td
                        style="background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                        Industry Threshold (Percentage)</td>
                    <td
                        style="background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                        Ratings</td>
                </tr>

                <?php foreach ($color_range as $cr) { ?>
                    <tr>
                        <td style="height:30px;font-size:13px;line-height:24px;text-align:center;">
                            <?php echo $cr->title; ?>
                        </td>
                        <td style="height:30px;font-size:13px;line-height:24px;text-align:center;">
                            <?php echo $cr->rating; ?>
                        </td>
                    </tr>
                <?php } ?>
                <!-- End -->
                <!-- <tr>
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
            </tr> -->
            </table>
        </td>
    </tr>
</table>
<!-- PART A -->
<table style="width:100%">
    <tr>
        <td>
            <?php $label = ($assessment_type == 2) ? 'Audio' : 'Video'; ?>
            <div class="video-title">
                <p>Part A: Reference
                    <?= $label ?> vs. Your
                    <?= $label ?>
                </p>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <table border="1" style="width:100%">
                <?php 
                $width = ($reference_video_right && $best_video_right) ? '30%' : '45%';
                if ($show_ranking == 1 or $show_ranking == "1") { ?>
                    <tr>
                        <?php if ($reference_video_right || $best_video_right) { ?>
                            <td
                                style="width:10%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                                S No.</td>
                            <?php if ($reference_video_right) { ?>
                            <td
                                style="width:<?= $width ?>;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                                Reference <?= $label ?></td>
                            <?php }
                            if ($best_video_right) { ?>
                            <td
                                style="width:<?= $width ?>;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                                Best <?= $label ?></td>
                            <?php } ?>
                            <td
                                style="width:<?= $width ?>;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                                Your <?= $label ?></td>
                        <?php } else { ?>
                            <td
                                style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                                S No.</td>
                            <td
                                style="width:80%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                                Your <?= $label ?></td>
                        <?php } ?>
                    </tr>
                    <?php foreach ($best_video_list as $bvl) { ?>
                        <tr>
                            <?php if ($reference_video_right || $best_video_right) { ?>
                                <td
                                    style="width:10%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;">
                                    <?php echo $bvl['question_series']; ?>
                                </td>
                                <?php if ($reference_video_right) { ?>
                                    <td
                                        style="background-color:#e4e4e4;width:<?= $width ?>;height:30px;font-size:13px;font-weight:normal;line-height:14px;text-align:center;">
                                        <a style="color: #232323;text-decoration:none;"
                                            href="<?php echo isset($bvl['refe_video_url']) ? $bvl['refe_video_url'] : ''; ?>">
                                            <?php echo isset($bvl['refe_video_url']) ? $bvl['refe_video_url'] : ''; ?>
                                        </a></td>
                                <?php }
                                if ($best_video_right) { ?>
                                    <td
                                        style="width:<?= $width ?>;height:30px;font-size:13px;font-weight:normal;line-height:14px;text-align:center;">
                                        <a style="color: #232323;text-decoration:none;" href="<?php echo $bvl['best_vimeo_url']; ?>">
                                            <?php echo $bvl['best_vimeo_url']; ?>
                                        </a></td>
                                <?php } ?>
                                <td
                                    style="background-color:#e4e4e4;width:<?= $width ?>;height:30px;font-size:13px;font-weight:normal;line-height:14px;text-align:center;">
                                    <a style="color: #232323;text-decoration:none;" href="<?php echo $bvl['your_vimeo_url']; ?>">
                                        <?php echo $bvl['your_vimeo_url']; ?>
                                    </a></td>
                            <?php } else { ?>
                                <td
                                    style="width:20%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;">
                                    <?php echo $bvl['question_series']; ?>
                                </td>
                                <td
                                    style="background-color:#e4e4e4;width:80%;height:30px;font-size:13px;font-weight:normal;line-height:14px;text-align:center;">
                                    <a style="color: #232323;text-decoration:none;" href="<?php echo $bvl['your_vimeo_url']; ?>">
                                        <?php echo $bvl['your_vimeo_url']; ?>
                                    </a></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <?php if ($reference_video_right) { ?>
                            <td
                                style="width:10%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                                S No.</td>
                            <td
                                style="width:45%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                                Reference <?= $label ?></td>
                            <td
                                style="width:45%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                                Your <?= $label ?></td>
                        <?php } else { ?>
                            <td
                                style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                                S No.</td>
                            <td
                                style="width:80%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                                Your <?= $label ?></td>
                        <?php } ?>
                    </tr>
                    <?php foreach ($best_video_list as $bvl) { ?>
                        <tr>
                            <?php if ($reference_video_right) { ?>
                                <td
                                    style="width:10%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;">
                                    <?php echo $bvl['question_series']; ?>
                                </td>
                                <td
                                    style="background-color:#e4e4e4;width:45%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;">
                                    <a style="color: #232323;text-decoration:none;"
                                        href="<?php echo isset($bvl['refe_video_url']) ? $bvl['refe_video_url'] : ''; ?>">
                                        <?php echo isset($bvl['refe_video_url']) ? $bvl['refe_video_url'] : ''; ?>
                                    </a></td>
                                <td
                                    style="background-color:#e4e4e4;width:45%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;">
                                    <a style="color: #232323;text-decoration:none;" href="<?php echo $bvl['your_vimeo_url']; ?>">
                                        <?php echo $bvl['your_vimeo_url']; ?>
                                    </a></td>
                            <?php } else { ?>
                                <td
                                    style="width:20%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;">
                                    <?php echo $bvl['question_series']; ?>
                                </td>
                                <td
                                    style="background-color:#e4e4e4;width:80%;height:30px;font-size:13px;font-weight:normal;line-height:24px;text-align:center;">
                                    <a style="color: #232323;text-decoration:none;" href="<?php echo $bvl['your_vimeo_url']; ?>">
                                        <?php echo $bvl['your_vimeo_url']; ?>
                                    </a></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </table>
        </td>
    </tr>
</table>

<br pagebreak="true" />
<!-- HEADER -->

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
                <?php if ($show_ranking == 1 or $show_ranking == "1") { ?>
                    <tr>
                        <td
                            style="width:10%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            S No.</td>
                        <td
                            style="width:53%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            Question Wise report for participant</td>
                        <td
                            style="width:17%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            Your Score</td>
                        <td
                            style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            Highest Score</td>
                        <!-- <td style="width:15%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Lowest Score</td> -->
                    </tr>
                    <?php foreach ($questions_list as $que) { ?>
                        <tr nobr="true">
                            <td style="width:10%;height:30px;font-size:13px;line-height:24px;text-align:center;">
                                <?php echo $que['question_series']; ?>
                            </td>
                            <td style="width:53%;height:30px;font-size:13px;line-height:24px;text-align:left;">
                                <?php echo $que['question']; ?>
                            </td>
                            <td
                                style="background-color:#e4e4e4;width:17%;height:30px;font-size:13px;line-height:24px;text-align:center;">
                                <?php echo ($que['your_score'] <= 0 and $que['failed_counter_your'] == 3) ? "Jarvis Unable To Process" : number_format($que['your_score'], 2, '.', '') . '%'; ?>
                            </td>
                            <td
                                style="background-color:#e4e4e4;width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;">
                                <?php echo ($que['highest_score'] <= 0 and $que['failed_counter_max'] == 3) ? "Jarvis Unable To Process" : number_format($que['highest_score'], 2, '.', '') . '%'; ?>
                            </td>
                            <!-- <td style="background-color:#e4e4e4;width:15%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo ($que['lowest_score'] <= 0 and $que['failed_counter_min'] == 3) ? "Jarvis Unable To Process" : number_format($que['lowest_score'], 2, '.', '') . '%'; ?></td> -->
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td
                            style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            S No.</td>
                        <td
                            style="width:60%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            Question Wise report for participant</td>
                        <td
                            style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            Your Score</td>
                    </tr>
                    <?php foreach ($questions_list as $que) { ?>
                        <tr nobr="true">
                            <td style="width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;">
                                <?php echo $que['question_series']; ?>
                            </td>
                            <td style="width:60%;height:30px;font-size:13px;line-height:24px;text-align:left;">
                                <?php echo $que['question']; ?>
                            </td>
                            <td
                                style="background-color:#e4e4e4;width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;">
                                <?php echo number_format($que['your_score'], 2, '.', '') . '%'; ?>
                            </td>
                        </tr>
                    <?php } ?>
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
            <div class="video-title">
                <p>Part C: Parameter Wise Assessments</p>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <table border="1" style="width:100%">
                <?php if ($show_ranking == 1 or $show_ranking == "1") { ?>
                    <tr>
                        <td
                            style="width:50%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            Parameter</td>
                        <td
                            style="width:25%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            Your Score</td>
                        <td
                            style="width:25%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            Highest Score</td>
                        <!-- <td style="width:20%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">Lowest Score</td> -->
                    </tr>
                    <?php foreach ($parameter_score as $ps) { ?>
                        <tr>
                            <td style="width:50%;height:30px;font-size:13px;line-height:24px;text-align:center;">
                                <?php echo ($ps['parameter_label_name'] != '') ? $ps['parameter_label_name'] : $ps['parameter_name']; ?>
                            </td>
                            <td
                                style="background-color:#e4e4e4;width:25%;height:30px;font-size:13px;line-height:24px;text-align:center;">
                                <?php echo number_format($ps['your_score'], 2, '.', '') . '%'; ?>
                            </td>
                            <td
                                style="background-color:#e4e4e4;width:25%;height:30px;font-size:13px;line-height:24px;text-align:center;">
                                <?php echo number_format($ps['highest_score'], 2, '.', '') . '%'; ?>
                            </td>
                            <!-- <td style="background-color:#e4e4e4;width:20%;height:30px;font-size:13px;line-height:24px;text-align:center;"><?php echo number_format($ps['lowest_score'], 2, '.', '') . '%'; ?></td> -->
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td
                            style="width:70%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            Parameter</td>
                        <td
                            style="width:30%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                            Your Score</td>
                    </tr>
                    <?php foreach ($parameter_score as $ps) { ?>
                        <tr>
                            <td style="width:70%;height:30px;font-size:13px;line-height:24px;text-align:center;">
                                <?php echo ($ps['parameter_label_name'] != '') ? $ps['parameter_label_name'] : $ps['parameter_name']; ?>
                            </td>
                            <td
                                style="background-color:#e4e4e4;width:30%;height:30px;font-size:13px;line-height:24px;text-align:center;">
                                <?php echo number_format($ps['your_score'], 2, '.', '') . '%'; ?>
                            </td>
                        </tr>
                    <?php } ?>
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
                    <td
                        style="width:50%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                        Parameter</td>
                    <td
                        style="width:50%;background-color:#004369;color:#ffffff;height:30px;font-size:15px;font-weight:bold;line-height:24px;text-align:center;">
                        Explanation</td>
                </tr>
                <tr>
                    <td
                        style="width:50%;height:30px;font-size:13px;font-weight:bold;line-height:110px;text-align:center;">
                        Product Knowledge</td>
                    <td style="width:50%;height:30px;font-size:13px;line-height:16px;">AI Models are trained on the
                        basis of the best answer or expected answer to a particular question. The AI Model maps the
                        intent of what has been actually spoken by the candidate to the best answer provided. Model
                        trained to take into account, synonyms, intent of answering, etc.
                    </td>
                </tr>
                <tr>
                    <td
                        style="width:50%;height:30px;font-size:13px;font-weight:bold;line-height:73px;text-align:center;">
                        Pace of Speech</td>
                    <td style="width:50%;height:30px;font-size:13px;line-height:16px;">AI Model trained to identify no.
                        of words spoken by person in a minute. Based on Ideal pace the deviation is mapped and and marks
                        given accordingly.
                    </td>
                </tr>
                <tr>
                    <td
                        style="width:50%;height:30px;font-size:13px;font-weight:bold;line-height:60px;text-align:center;">
                        Pitch</td>
                    <td style="width:50%;height:30px;font-size:13px;line-height:16px;">Pitch of a sound is a quality
                        that makes it possible to identify sounds as high or low. Pitch of sounds allows ordering on a
                        frequency-related scale.
                    </td>
                </tr>
                <tr>
                    <td
                        style="width:50%;height:30px;font-size:13px;font-weight:bold;line-height:90px;text-align:center;">
                        Body Language</td>
                    <td style="width:50%;height:30px;font-size:13px;line-height:16px;">Body language is one of the most
                        important parameters, to win the confidence of the customer. AI Model trained for recognizing
                        relevant parameters (facial expression and tone) for mapping body language.
                    </td>
                </tr>
                <tr>
                    <td
                        style="width:50%;height:30px;font-size:13px;font-weight:bold;line-height:60px;text-align:center;">
                        Voice Modulation</td>
                    <td style="width:50%;height:30px;font-size:13px;line-height:16px;">AI Model trained to see the way
                        in which the candidate modulates his or her voice, to ensure that the conversation is engaging
                        to the listener.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <p class="notes">Please note that whilst interpreting the results, the scores are not good or bad. They must
                always be viewed in relative and not in absolute terms. The accuracy of the results are contingent on
                the environment in which the simulation was played and the seriousness with which it was undertaken.</p>
        </td>
    </tr>
</table>

<br pagebreak="true" />
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
    <?php foreach ($partd_list as $partd) { ?>
        <tr>
            <td style="height:30px;line-height:30px;">
                <div style="height:30px;line-height:30px;">&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td style="width:100%;">
                <table style="width:100%;">
                    <tr nobr="true">
                        <td style="width:100%;font-size:13px;line-height:16px;">
                            <?php echo $partd['question_series'] . ') ' . $partd['question']; ?>
                        </td>
                    </tr>
                    <?php if (isset($partd['list'])) {
                        foreach ($partd['list'] as $pico) {
                            $icon = '';
                            if ($pico['tick_icons'] == 'green') {
                                $icon = '<img src="assets/images/green-tick.png" class="icon-image"/>';
                            }
                            if ($pico['tick_icons'] == 'yellow') {
                                $icon = '<img src="assets/images/yellow-tick.png" class="icon-image"/>';
                            }
                            if ($pico['tick_icons'] == 'red') {
                                $icon = '<img src="assets/images/red-tick.png" class="icon-image"/>';
                            }
                            ?>
                            <tr nobr="true">
                                <td style="width:4%;">
                                    <?php echo $icon; ?>
                                </td>
                                <td style="width:96%;font-size:13px;line-height:16px;vertical-align:top;">
                                    <?php echo trim($pico['sentance_keyword']); ?>
                                </td>
                            </tr>
                        <?php }
                    } ?>
                </table>
            </td>
        </tr>


    <?php } ?>
</table>