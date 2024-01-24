<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<div class="modal-header">
	<button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title">Choose Parameter/Sub Parameter</h4>
</div>
<div class="modal-body">
	<div style="width: 100%;height: auto;padding: 0px 20px 10px 20px;">
		<div style="padding: 10px 0px 10px 10px;background: #e9edef;font-size: 14px;font-weight: 600;">Selected Parameters:</div>
		<div id="selected_parameters" style="padding: 10px 0px 10px 20px;background: #e9edef;">&nbsp;</div>
	</div>
	<form name="frmSubParameter" id="frmSubParameter">
		<div class="portlet light">
			<div class="form-body">
				<input type="hidden" value="<?= $txn_id; ?>" id="txn_id" name="txn_id">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="">Parameter Name<span class="required"> * </span></label>
							<select id="dp_parameter_id" name="dp_parameter_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" onchange="load_subparameter(this.value);">
								<option value="">Please Select</option>
								<?php foreach ($parameters as $p) { ?>
									<option value="<?= $p->id; ?>"><?php echo $p->description; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="">Parameter Label Name<span class="required"> * </span></label>
							<select id="parameter_label_id" name="parameter_label_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
								<option value="">Please Select</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="">Parameter Weight</label>
							<input type="text" id="parameter_weight" name="parameter_weight" class="form-control input-sm" placeholder="Please add weight" style="width:100%" />
						</div>
					</div>
				</div>
				<table class="table table-striped table-bordered table-hover" id="SubParameterTable" width="100%">
					<thead>
						<tr>
							<!-- <th class="table-checkbox " style="width:8%">
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
									<input type="checkbox" class="checkboxes subparameter_all" name="subparameter_all"  id="chk" />
									<span></span>
                            	</label>
                            </th> -->
							<th style="width:8%">...</th>
							<th style="width:40%">Sub Parameter</th>
							<th style="width:20%">Type</th>
							<th style="width:32%;float:left;position: absolute;">Sentence / Keyword
								<!-- <span style="float:left;font-size:14px;color:red;text-transform: capitalize;position: relative;">Use | (pipe line) for multiple.</span> -->
							</th>
						</tr>
					</thead>
					<tbody id="sub_parameter_container">
						<tr>
							<td colspan="2"> To display sub-parameters, first choose parameter from the above list.</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-orange" onclick="pm_delete();">Remove All (Only Selected Parameter)</button>
	<button type="button" class="btn btn-orange" onclick="pm_submit(0,'<?php echo $edit_id; ?>');">Submit & New</button>
	<button type="button" class="btn btn-orange" onclick="pm_submit(1,'<?php echo $edit_id; ?>');">Submit & Close</button>
	<button type="button" class="btn btn-orange" id="parameter_close">Close</button>
</div>

<script type="text/javascript">
	var frm1 = document.frmSubParameter;
	var assessment_type = $('#assessment_type').val();
	$(document).ready(function() {
		customBlockUI();

		var html_selected_parameters = "";
		var temp_spjson = TempSubParameterArray.filter(function(obj) {
			if (parseInt(obj.txn_id) == parseInt($("#txn_id").val())) {
				return true;
			} else {
				return false;
			}
		});
		temp_spjson.forEach(function(item, index) {
			var n = html_selected_parameters.search(item.parameter_name);
			if (n < 0) {
				html_selected_parameters = html_selected_parameters + '<span class="label label-primary">' + item.parameter_name + '</span>&nbsp;&nbsp;';
			}
		});

		$("#selected_parameters").html(html_selected_parameters);

		$(".select2").select2();
		$('.subparameter_all').click(function() {
			if ($(this).is(':checked')) {
				$("input[name='chksp_list[]']").prop('checked', true);
				$("input[name='chksp_list[]']").trigger('change');
			} else {
				$("input[name='chksp_list[]']").prop('checked', false);
				$("input[name='chksp_list[]']").trigger('change');
			}
		});
		load_subparameter($("#dp_parameter_id").val());
		customunBlockUI();
	});

	function load_parameters_labels(parameter_id) {
		pdata = {
			parameter_id: parameter_id,
			assessment_type: "3"
		};
		$.ajax({
			type: "POST",
			data: pdata,
			async: false,
			url: "<?php echo base_url(); ?>create_trinity/ajax_parameters_labels",
			beforeSend: function() {
				customBlockUI();
			},
			success: function(Odata) {
				if (Odata != '') {
					var Oresult = jQuery.parseJSON(Odata);
					var json_parameter_label = Oresult['result'];
					var option = '<option value="">Please Select</option>';
					for (var i = 0; i < json_parameter_label.length; i++) {
						option += '<option value="' + json_parameter_label[i]['id'] + '" >' + json_parameter_label[i]['description'] + '</option>';
					}
					$('#parameter_label_id').empty();
					$('#parameter_label_id').append(option);
					var parameter_label_selected_data = TempSubParameterArray.filter(function(obj) {
						if ((parseInt(obj.txn_id) == parseInt($("#txn_id").val())) && (parseInt(obj.parameter_id) == parseInt(parameter_id))) {
							return true;
						} else {
							return false;
						}
					});
					if (Object.keys(parameter_label_selected_data).length > 0) {
						var _parameter_label_id = parameter_label_selected_data[0]['parameter_label_id'];
						$("#parameter_label_id").val(_parameter_label_id).trigger('change');
					}
				}
				customunBlockUI();
			}
		});
	}

	function load_subparameter(parameter_id) {
		load_parameters_labels(parameter_id);
		if ($('#dp_parameter_id').val() == "") {
			$('#parameter_label_name').val("");
		} else {
			parameter_weight = '';
			var temp_parameter_label_name = TempSubParameterArray.filter(function(obj) {
				if (parseInt(obj.parameter_id) == parseInt($('#dp_parameter_id').val()) && parseInt(obj.txn_id) == parseInt($('#txn_id').val())) {
					parameter_weight = obj.parameter_weight;
					return true;
				}
				if (parseInt(obj.parameter_id) == parseInt($('#dp_parameter_id').val())) {
					return true;
				} else {
					return false;
				}
			});

			console.log(temp_parameter_label_name);
			if (Object.keys(temp_parameter_label_name).length > 0) {
				$('#parameter_label_name').val(temp_parameter_label_name[0].parameter_label_name);
				$('#parameter_weight').val(parameter_weight);
			} else {
				// if ($('#parameter_label_name').val()==''){
				var parameter_data = $('#dp_parameter_id').select2('data');
				var parameter_name = parameter_data[0].text;
				$('#parameter_label_name').val(parameter_name);
				// }
			}
		}
		tdata = {
			txn_id: $('#txn_id').val(),
			parameter_id: parameter_id,
			sub_parameter: JSON.stringify(TempSubParameterArray),
			assessment_type: $('#assessment_type').val()
		};
		$.ajax({
			async: true,
			type: 'POST',
			url: Base_url + "create_trinity/datatable_subparameter_refresh",
			dataType: "json",
			data: tdata,
			beforeSend: function(x) {
				customBlockUI();
			},
			success: function(Odata) {
				if (Odata.html != '') {
					$('#sub_parameter_container').html(Odata.html);
					$("input[name='chksp_list[]']").trigger('change');
				}
				get_weight();
				customunBlockUI();
			}
		});
	}
	$('#parameter_close').click(function() {
		$('#LoadModalFilter').modal('toggle');
	});
</script>
<script>
	<?php
	if (count((array)$parameter_subparameter) > 0) {
		foreach ($parameter_subparameter as $pstran) {
	?>
			var push_value = {};
			push_value['language_id'] = "<?php echo $pstran->language_id; ?>";
			push_value['txn_id'] = "<?php echo $pstran->txn_id; ?>";
			push_value['parameter_id'] = "<?php echo $pstran->parameter_id; ?>";
			push_value['parameter_name'] = "<?php echo $pstran->parameter_name; ?>";
			push_value['parameter_label_id'] = "<?php echo $pstran->parameter_label_id; ?>";
			push_value['parameter_label_name'] = "<?php echo $pstran->parameter_label_name; ?>";
			push_value['subparameter_id'] = "<?php echo $pstran->sub_parameter_id; ?>";
			push_value['subparameter_name'] = "<?php echo $pstran->sub_parameter_name; ?>";
			push_value['type_id'] = "<?php echo $pstran->type_id > 0 ? $pstran->type_id : ''; ?>";
			push_value['type_name'] = "<?php echo $pstran->type_name; ?>";
			push_value['sentence_keyword'] = "<?php echo htmlspecialchars($pstran->sentence_keyword); ?>";
			push_value['parameter_weight'] = "<?php echo $pstran->parameter_weight; ?>";
			TempSubParameterArray.push(push_value);
			div_html = printonscreen_keyword_sentence(<?php echo $pstran->txn_id; ?>, <?php echo $pstran->parameter_id; ?>);
			var div_element = "#paramsub" + <?php echo $pstran->txn_id; ?>;
			$(div_element).empty();
			$(div_element).html('');
			$(div_element).html(div_html);

	<?php
		}
	}
	?>
</script>