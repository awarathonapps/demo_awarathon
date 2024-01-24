<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"> Upload Refrence Video</h4>
</div>
<div class="modal-body">

    <div class="portlet light" style="margin-bottom: 0px!important;">
        <div class="form-body">
            <div class="form-container">

                <form action="<?php echo base_url(); ?>assessment_create/upload_video_ref" id="uploadForm"
                    name="frmupload" method="post" enctype="multipart/form-data">
                    <!-- my part -->
                    <div class="row" style="margin-bottom: 5px;">
                        <?php if ($this->session->flashdata('flash_message')) { ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                <?php echo $this->session->flashdata('flash_message'); ?>
                            </div>
                        <?php } ?>
                        <div class="alert alert-danger display-hide" id="errordiv">
                            <button class="close" data-close="alert"></button>
                            You have some form errors. Please check below.
                            <br><span id="errorlog"></span>
                        </div>
                        <div class="col-md-12 text-center">
                            <!-- <center> -->
                            <div class="form-group ">
                                <label style="font-weight: bold;font-size: 15px;">Upload Reference Video in the section
                                    below</label><br><br>
                                <i class="fa fa-file-video-o" aria-hidden="true" style="font-size: 40px;"></i>
                            </div>
                            <!-- </center> -->
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="">Question Title<span class="required"> * </span></label>
                                <input type="hidden" name="video_title" id="video_title"
                                    value="<?php echo isset($result['video_title']) ? $result['video_title'] : ''; ?>"
                                    class="form-control input-sm">
                                <input type="text" name="video_title1" id="video_title1"
                                    value="<?php echo isset($result['video_title']) ? $result['video_title'] : ''; ?>"
                                    class="form-control input-sm" disabled>
                                <input type="hidden" name="ref_id" id="ref_id"
                                    value="<?php echo (isset($result['id']) ? $result['id'] : ''); ?>"
                                    class="form-control input-sm">
                                <input type="hidden" name="tr_no" id="tr_no" value="<?php echo $tr_no; ?>"
                                    class="form-control input-sm">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Upload Video File</label>
                                <div id="queVideo" class="form-group" onclick="check_url()">
                                    <input type="file" id="video_file" name="file" style="display:none;"
                                        value="<?php echo (isset($result['video_url']) ? $result['video_url'] : ''); ?>"
                                        accept="video/mp4,video/x-m4v,video/*" />
                                    <div class="form-control fileinput fileinput-exists"
                                        style="border: none;height:auto;padding:0" data-provides="fileinput"
                                        onclick="document.getElementById('video_file').click()">
                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                            style="width: 100%; max-height: 200px;">
                                            <img id="question_preview"
                                                src="<?php echo base_url() . 'assets/uploads/no_video.png'; ?>"
                                                width="50%" height="200" alt="No Image" />
                                        </div>
                                    </div>
                                    <!-- File Show -->
                                    <div class="form-control fileinput fileinput-new" id="video_file_details"
                                        style="width: 100%;border: none;height:auto;padding-left: 0px; display:none"
                                        data-provides="fileinput">
                                        <div class="input-group input-large">
                                            <div class="form-control uneditable-input span3">
                                                <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="filename"
                                                    id="filename"></span>
                                            </div>
                                            <span class="input-group-addon btn default btn-file">
                                                <span class="fileinput" id="change_option"
                                                    onclick="document.getElementById('video_file').click()">
                                                    Change </span>
                                            </span>
                                            <a id="RemoveFile" onclick="remove_file()"
                                                class="input-group-addon btn red">Remove</a>
                                        </div>

                                    </div>
                                    <span class="text-muted" style="color:red">Note : File not more than 100 MB.</span>
                                    <!-- File show -->
                                    <div class='progress' id="progressDivId">
                                        <div class='progress-bar' id='progressBar'></div>
                                        <div class='percent' id='percent'>0%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center" id="or_option">
                            <label class="" style="font-weight: bold;font-size: 15px;"> OR </label>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" id="video_url_upload">
                                <label>Video Url</label>
                                <input type="text" name="video_url" id="video_url"
                                    value="<?php echo (isset($result['video_url']) ? $result['video_url'] : ''); ?>"
                                    class="form-control input-sm">
                                <label style="color: red;">Vimeo url
                                    Format:https//player.vimeo.com/video/655793434?h=62434343</label>
                            </div>
                        </div>
                        <div>
                            <input id="submitButton" class="btn btn-orange" type="submit" name='btnSubmit'
                                value="Submit" />
                            <button type="button" data-dismiss="modal" class="btn btn-default btn-cons">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- <div class="modal-footer"> -->
<!-- <button type="button" class="btn btn-orange" onclick="confirm_video_ref();">Submit</button> -->
<!-- <button type="button" data-dismiss="modal" class="btn btn-default btn-cons">Cancel</button> -->
<!-- </div> -->
<script type="text/javascript" src="<?php echo base_url(); ?>/assets/customjs/jquery.form.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#submitButton').click(function () {
            var video_title = $('#video_title').val();
            var video_file = $('#video_file').val();
            var video_url = $('#video_url').val();

            if (video_title == "") {
                ShowAlret("Please Enter Video Title!", 'error');
                return false;
            }
            // if (video_file == "" && video_url == "") {
            //     ShowAlret("Please select video file!", 'error');
            //     return false;
            // }

            $('#uploadForm').ajaxForm({
                // target: '#outputImage',
                url: '<?php echo base_url(); ?>assessment_create/upload_video_ref',
                beforeSubmit: function () {
                    // $("#outputImage").hide();
                    $("#progressDivId").css("display", "block");
                    var percentValue = '0%';

                    $('#progressBar').width(percentValue);
                    $('#percent').html(percentValue);
                    $("#video_file").prop('disabled', true);
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    var percentValue = percentComplete + '%';
                    if ($("#video_file").val() == "" && $("#video_url").val() == '') {
                        $("#progressDivId").hide();
                    }
                    $("#progressBar").animate({
                        width: '' + percentValue + ''
                    }, {
                        duration: 5000,
                        easing: "linear",
                        step: function (x) {
                            percentText = Math.round(x * 100 / percentComplete);
                            percentText = percentText > 100 ? 100 : percentText;
                            $("#percent").text(percentText + "%");
                            if (percentText == "100") {
                                // $("#outputImage").show();
                            }
                        }
                    });
                },
                error: function (response, status, e) {
                    alert('Oops something went.');
                },

                complete: function (xhr) {
                    if (xhr.responseText && xhr.responseText != "error") {
                        var Data = $.parseJSON(xhr.responseText);
                        ShowAlret(Data['Msg'], 'success');
                        $('#ref_video_form').val('');
                        $('#removefileBtn').show();
                        $('.file_border').css("display", "visible");
                        $('#progressDivId').hide();
                        $("#video_file").prop('disabled', false);
                        if (Data['reports_rights']) {
                            $(".reports_rights").select2("enable");
                        } else {
                            $('.reports_rights').val('').trigger('change');
                            // $(".reports_rights").select2("disable");
                            $(".reports_rights").prop('disabled', true);
                        }
                        refrence_video_table(Data['que_id'], Data['tr_no']);
                    } else {

                        // $("#outputImage").show();
                        // $("#outputImage").html("<div class='error'>Problem in uploading file.</div>");
                        App.scrollTo(form_error, -200);
                        $("#progressBar").stop();
                    }
                }
            });
        });
    });

    if ($('#video_file').val() == '') {
        $('#removefileBtn').hide();
    }
    $('#video_url').click(function () {
        if ($('#video_file').val() != '') {
            $("#video_url").prop("disabled", true);
        } else {
            $("#video_url").prop('disabled', false);
        }
    });


    function check_url() {
        if ($('#video_url').val() != '') {
            $("#queVideo").prop('disabled', true);
        } else {
            $("#queVideo").prop('disabled', false);
        }
    }

    $('#video_file').change(function () {
        var filename = $(this).val().split('\\').pop();
        $('#filename').text(filename);
        $('#video_file_details').show();
    });


    function remove_file() {
        $('#filename').text('');
        $('#video_file').val('');
        $("#progressDivId").hide();
        $('#progressBar').width(0);
        $('#percent').val(0);
        $('#video_file_details').hide();
        // $("#video_file").prop('disabled', false);
    }
</script>