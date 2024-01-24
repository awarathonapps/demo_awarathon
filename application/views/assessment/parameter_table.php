<?php if (!$is_supervisor) { ?>
    <style> 
        .rating:not(:checked) > label:hover, /* hover current star */
        .rating:not(:checked) > label:hover ~ label { 
            background-color:#eb3a12 !important;
            cursor:pointer;
        }
    </style> 

    <style> 
        .rating > input:checked + label:hover, /* hover current star when changing rating */
        .rating > label:hover ~ input:checked ~ label, /* lighten current selection */
        .rating > input:checked ~ label:hover ~ label { 
            background-color:#eb3a12 !important;
            cursor:pointer;
        } 
    </style> 
<?php } ?>
<div class="col-md-12 margin-top-10">
    <?php //echo $video_screen; ?>
    <?php if (count((array)$video_data) > 0) {
        if($assessment_type == 2){
            $url =isset($video_data->audio_id)?$video_data->audio_id:"";
            if($url!='')
            {
                ?>
                <audio controls style="width: 100%;">
                    <source src="https://aiapi.awarathon.com/audio/<?php echo $url; ?>" type="audio/wav">
                    <source src="https://aiapi.awarathon.com/audio/<?php echo $url; ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                    </audio>
                <input type="hidden" id="ass_result_id" name="ass_result_id" value="<?php echo $video_data->id; ?>"/>
                <?php
            }
        } else{
            $url =($video_data->vimeo_uri !=$video_data->video_url ? $video_data->vimeo_uri.'&title=0' :$video_data->video_url.'?title=0');
            ?>
            <iframe width="520" height="250"  src="https://player.vimeo.com/video/<?php echo $url; ?>&amp;byline=0&amp;portrait=0&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=139753" frameborder="0" allowFullScreen mozallowfullscreen webkitAllowFullScreen></iframe>
            <input type="hidden" id="ass_result_id" name="ass_result_id" value="<?php echo $video_data->id; ?>"/>
            <?php
        }
    }else {
        echo "<h3>No Content</h3>";
    }
    ?>                        
</div>
<div class="col-md-12">
    <h5><b id="selectedquestion"><?php echo $Question; ?></b></h5>
</div>
<div class="col-md-12" id="parameter_table_div">
    <table  class="table table-striped table-bordered table-hover" width="100%" id="rating_table" name="rating_table">
        <col width="100px" />
        <col width="500px" />
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Rating</th>                                    
            </tr>
        </thead>
        <tbody>        
            <?php foreach ($QParameterData as $key => $val) { ?>
                <tr>
                    <td><?php echo isset($val->parameter_label_name)?$val->parameter_label_name:$val->parameter; ?></td>
                    <?php if (isset($ratingstyle) && $ratingstyle == 2) { ?>
                        <td id="star_disable" >
                            <div ><input type="text" class="rating_range" id="<?php echo 'field' . $val->id; ?>" name="rating[<?php echo $val->id; ?>]" value="<?php echo $val->score; ?>"  /></div>
                        </td>
                    <?php } else { ?>
                        <td id="star_disable" >
                            <fieldset class="rating">
                                <?php
                                for ($i = $val->weight_value; $i > 0; $i--) {
                                    $radio_id = 'field' . $val->id . '_star' . $i;
                                    //$click_fun = $cnt_rate == 0 ? 'get_star("'.$radio_id.'")': '';
                                    ?>                        
                                    <input type="radio" id="<?php echo 'field' . $val->id . '_star' . $i; ?>" name="rating[<?php echo $val->id; ?>]" value="<?php echo $i; ?>" <?php echo (!$is_supervisor ? 'onclick="Save_rating()"' : ''); ?>/>
                                    <label id="<?php echo 'field' . $val->id . '_star' . $i; ?>" class="full" for="<?php echo 'field' . $val->id . '_star' . $i; ?>" onclick="get_star('<?php echo (!$is_supervisor ? $radio_id : ''); ?>')"></label>
                                <?php } ?>
                            </fieldset>
                        </td>
                    <?php } ?>


                </tr>    
            <?php } ?>
        </tbody>
    </table>
    <input type="hidden" id="cnt_para" name="cnt_para" value="<?php echo $cnd_para; ?>"/>
</div>
<script>
    jQuery(document).ready(function () {
<?php
if (count((array)$para_rating) > 0) {
    foreach ($para_rating as $key => $val) {
        $str = 'field' . $key . '_star' . $val;?>
            get_star('<?php echo $str; ?>');
        <?php
    }
}?>
});
<?php if (isset($ratingstyle) && $ratingstyle == 2) { ?>
        $(".rating_range").ionRangeSlider({
            skin: "big",
            min: 0,
            max: 100,
            //step: 0.1,
            postfix: "%",
            prettify: true,
            grid: true,
            from_fixed: <?php echo $is_supervisor; ?>,
            onFinish: function (event) {
                <?php if(!$is_supervisor){ ?>
                Save_rating();
                <?php } ?>
            }
        });
<?php } ?>
</script>    