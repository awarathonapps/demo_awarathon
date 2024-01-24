<?php if($cnt_rate == 0 && $mode==2){ ?>
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
    <?php if (count($video_data)>0 ) {?>
            <iframe width="520" height="250"  src="https://player.vimeo.com/video/<?php echo $video_data->video_url; ?>?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=139753" frameborder="0" allowFullScreen mozallowfullscreen webkitAllowFullScreen></iframe>
            <input type="hidden" id="ass_result_id" name="ass_result_id" value="<?php echo $video_data->id; ?>"/>
    <?php }else{
        echo "<h3>No Video</h3>";
    } ?>                        
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
                    <td><?php echo $val->parameter; ?></td>
                    <td id="star_disable" >
                        <fieldset class="rating">
                            <?php for ($i = $val->weight_value; $i > 0; $i--) { 
                                $radio_id = 'field' . $val->id . '_star' . $i;
                                $click_fun = $cnt_rate == 0 ? 'get_star("'.$radio_id.'")': '';
                                ?>                        
                                <input type="radio" id="<?php echo 'field' . $val->id . '_star' . $i; ?>" name="rating[<?php echo $val->id; ?>]" value="<?php echo $i; ?>" onclick="<?php echo ($cnt_rate == 0 && $mode==2 ? 'Save_rating(0)' : ' '); ?>"/>
                                <label id="<?php echo 'field' . $val->id . '_star' . $i; ?>" class="full" for="<?php echo 'field' . $val->id . '_star' . $i; ?>" onclick="get_star('<?php echo ($cnt_rate == 0 && $mode==2 ? $radio_id : ''); ?>')"></label>
                            <?php } ?>
                        </fieldset>
                    </td>
                </tr>    
            <?php } ?>
        </tbody>
    </table>
</div>
<script>
    jQuery(document).ready(function () {
<?php
if (count($para_rating) > 0) {
    foreach ($para_rating as $key => $val) {
        $str = 'field' . $key . '_star' . $val;
        ?>
                get_star('<?php echo $str; ?>');
        <?php
    }
}
?>
    });
    function get_star(starid) {
        if(starid !=''){
        $('#' + starid).parent().find("label").css({"background-color": "#D8D8D8"});
        $('#' + starid).css({"background-color": "#eb3a12"});
        $('#' + starid).nextAll().css({"background-color": "#eb3a12"});
        }

    }
</script>    