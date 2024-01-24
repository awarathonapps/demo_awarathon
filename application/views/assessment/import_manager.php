<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Import Manager</h4>
    
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="alert alert-success MedicineReturnSuccess display-hide" id="successDiv">
        <button class="close" data-close="alert"></button>
        <span id="SuccessMsg"></span>
    </div>
    <div class="alert alert-danger  display-hide" id="modalerrordiv">
        <button class="close" data-close="alert"></button>
        <span id="modalerrorlog"></span>
    </div>
        <form name="ImportForm" id="ImportForm" >
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">    
                        <div class="form-group">
                            <label>Choose File<span class="required"> * </span></label>
                            <div class="form-control fileinput fileinput-new" style="width: 100%;border: none;height:auto;" data-provides="fileinput">
                                <div class="input-group input-large">
                                    <div class="form-control uneditable-input span3" data-trigger="fileinput">
                                        <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                        </span>
                                    </div>
                                    <span class="input-group-addon btn default btn-file">
                                        <span class="fileinput-new">
                                            Select file </span>
                                        <span class="fileinput-exists">
                                            Change </span>
                                        <input type="file" name="filename" id="filename" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                    </span>
                                    <a href="javascript:;" id="RemoveFile" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                        Remove </a>
                                </div>
                            </div><br/>
                            <span class="text-muted">(only .csv allowed)</span>
                        </div>
                    </div>
                    <div class="col-md-6">    
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <a href="<?php echo base_url() . 'assessment/manager_samplecsv' ?>" class="form-control" style="    border: none;height:auto;" ><strong>Download Sample csv File</strong></a>
                        </div>
                    </div>
                </div>
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Notes</h3>
                    </div>
                    <div class="panel-body">
                        <ul>
                            <li>Upload Users Data through csv file.</li>
                            <li>csv file format must be same as sample csv format.</li>
                            <li>Do not modify or delete the Columns of sample csv.</li>
                            <li>In sample csv file * is mandatory Fields.</li>
                        </ul>
                    </div>
                </div>
            </div>          
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-orange" onclick="UploadXlsManager();" >Confirm</button>
    </div>
