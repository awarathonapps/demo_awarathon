<style>
    #parameter_weight_modal {
        display: block;
        padding-right: 17px;
        width: 57%;
        margin-top: 20px;
        margin-left: 22%;
        height: 80%;
        background-color: white;
    }
</style>
<div id="parameter_weight_modal" class="modal" style="display:none">
    <div class="modal-header">
        <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Map Parameter Weight</h4>
    </div>
    <div class="modal-body">
        <form name="parameter_form" id="parameter_form">
            <input type="hidden" name="editid" value="" id="editid">
            <div class="portlet light">
                <div class="form-body">
                    <table class="table table-striped table-bordered table-hover" id="parameterweight" width="100%">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Weightage</th>
                            </tr>
                        </thead>
                        <tbody id="parameterweight">
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-orange" onclick="parameter_weight_submit();">Confirm</button>

                </div>
            </div>
        </form>
    </div>
</div>