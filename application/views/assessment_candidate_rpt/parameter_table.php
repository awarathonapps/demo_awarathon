
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
        <col width="500px" />
        <col width="100px" />
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Score</th>                                    
            </tr>
        </thead>
        <tbody>        
            <?php foreach ($QParameterData as $key => $val) { ?>
                <tr>
                    <td><?php echo $val->parameter_label_name; ?></td>
                    <td>
                       <?php echo ((isset($para_rating[$val->id])) ? ($para_rating[$val->id]*100/$val->weight_value).'%' : '0%'); ?> 
                    </td>
                </tr>    
            <?php } ?>
        </tbody>
    </table>
</div>
<script>
    jQuery(document).ready(function () {

    });
    
</script>    