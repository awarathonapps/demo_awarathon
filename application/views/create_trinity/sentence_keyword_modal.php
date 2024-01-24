<!-- Modal popup content -->
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&times;</button> 
	<h4 class="modal-title">Add Sentence/Keyword</h4>                                                             
</div>
<div class="modal-body">
<input type="hidden" id="remove_id" value="">						
<table class="table table-bordered" id="dynamic_field">    
	<tr>    
		<td>
			<input type="hidden" name="ivalid" id="ivalid" ival="0"/>
			<input type="hidden" name="subparameter_id" id="subparameter_id" row_val=""/>
			<input type="text" name="name[]" id="sentence_keyword_0" class="form-control sentence_keyword" required="" />
		</td>    
		<td>
			<button type="button" name="add" id="add_sentence_keyword" class="add_sentence_keyword btn btn-success"><i class="fa fa-plus fa-xs" aria-hidden="true"></i></button>
		</td>    
	</tr>    
</table>
</div>   
<div class="modal-footer">
	<button type="button" class="btn btn-orange" id="save_keyword_sentence">Submit</button> 
	<button type="button" class="btn btn-orange" id="Mymodalid_close">Close</button>                               
</div>

<script type="text/javascript">
	$('#Mymodalid_close').click(function(){
		$('#Mymodalid').modal('toggle');
		return false;
	});
	$(document).ready(function(){
		var row_val = "<?= $row_val ?>";
		var sentence_keyword_textarea=$("#sentkey"+row_val).val();
		var sentence_keyword_string;        //final concated string which is not stored in database
		if(sentence_keyword_textarea!=""){
			var sentence_keyword_array=sentence_keyword_textarea.split('|');
			sentence_keyword_array_length=sentence_keyword_array.length-1;
			$("#ivalid").attr("ival",sentence_keyword_array_length);
			$("#dynamic_field").empty();
			$('#dynamic_field').append('<tr><td><input type="hidden" name="ivalid" id="ivalid" ival="0"/><input type="hidden" name="subparameter_id" id="subparameter_id" row_val=""/><input type="text" name="name[]" id="sentence_keyword_0" class="form-control sentence_keyword" required="" /></td>    <td><button type="button" name="add" id="add_sentence_keyword" class="add_sentence_keyword btn btn-success"><i class="fa fa-plus fa-xs" aria-hidden="true"></i></button></td></tr>');
			for(i=0;i<=sentence_keyword_array_length;i++){
				if(i==0){
					$("#sentence_keyword_0").val(sentence_keyword_array[i]);
				}else{
					$('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td style="width:90%"><input type="text" value="" name="name[]" id="sentence_keyword_'+i+'"  class="form-control" required /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
					$('#sentence_keyword_'+i).val(sentence_keyword_array[i]);
					// $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td style="width:90%"><input type="text" value="'+sentence_keyword_array[i]+'" name="name[]" id="sentence_keyword_'+i+'"  class="form-control" required /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');       
				}
			}
			$("#ivalid").attr("ival",sentence_keyword_array_length);
			$("#subparameter_id").attr("row_val",row_val);
			$('#Mymodalid').modal('show');
		}else{
			$("#ivalid").attr("ival",0);
			$("#subparameter_id").attr("row_val",row_val);
			$('#Mymodalid').modal('show');
		}
		$('#add_sentence_keyword').click(function(){
			var j=$("#ivalid").attr("ival");
			j++;
			var row_val = $("#subparameter_id").attr("row_val"); 
			var sentence_keyword_textarea=$("#sentkey"+row_val).val();
			$('#dynamic_field').append('<tr id="row'+j+'" class="dynamic-added"><td><input type="text" name="name[]" id="sentence_keyword_'+j+'"  class="form-control" required /></td><td><button type="button" name="remove" id="'+j+'" class="btn btn-danger btn_remove">X</button></td></tr>');
			$("#ivalid").attr("ival",j);
		});
		
		$(document).on('click', '.btn_remove', function(){
		   var button_id = $(this).attr("id");     
		   remove_id=$("#remove_id").val();
		   if(remove_id==""){
				remove_id=button_id;
		   }else{
				remove_id=remove_id+","+button_id;
		   }
		   $("#remove_id").val(remove_id);
		   $('#row'+button_id+'').remove();    
		}); 
		
		$('#save_keyword_sentence').click(function(){
			var ival = $("#ivalid").attr("ival");
			sentence_keyword_string="";
			for (var i = 0; i <= ival; i++){
				delete_id=$("#remove_id").val();
				if(delete_id!=""){
					var delete_id_array = delete_id.split(',');
					$.each(delete_id_array, function( index, value ) 
					{
						$('#sentence_keyword_'+value).remove();
					});
					if ($('#sentence_keyword_'+i).length){
						if(!$("#sentence_keyword_"+i).val()){
							ShowAlret("Please fill the empty fields!", 'error');
							return false;
						}
						sentence_keyword_string=sentence_keyword_string+$("#sentence_keyword_"+i).val()+"|";
					}
				}else{
					if(!$("#sentence_keyword_"+i).val()){
						ShowAlret("Please fill the empty fields!", 'error');
						return false;
					}
					sentence_keyword_string=sentence_keyword_string+$("#sentence_keyword_"+i).val()+"|";
				}
			}
			sentence_keyword_string= sentence_keyword_string.substring(0, sentence_keyword_string.length - 1);
			var subparameter_id= $("#subparameter_id").attr("row_val");
			$("#sentkey"+subparameter_id).val(sentence_keyword_string);
			$('#Mymodalid').modal('hide');
			$("#remove_id").val("");
		});
	});
</script>