function block_ui() {
    KTApp.blockPage({
        overlayColor: '#000000',
        state       : 'danger',
        message     : 'Please wait...'
    });
}
function unblock_ui() {
    KTApp.unblockPage();
}
function block_modal_ui() {
    KTApp.block('#LoadModalFilter .modal-content', {
        overlayColor: '#000000',
        state       : 'danger',
        message     : 'Please wait...'
    });
}
function unblock_modal_ui() {
    KTApp.unblock('#LoadModalFilter .modal-content');
}
function customAlert(Scontent){
    $.alert({
        title: 'Alert!',
        content: Scontent,
    });
}
function AlertBox(stext,type){
    KTUtil.scrollTop();
    // $('#mw-notification').show();
    // $('#mw-notification-text').html(stext);
    // if(type=='success'){
    //     $("#mw-notification").addClass("private-alert--success");
    // }else{
    //     $("#mw-notification").addClass("private-alert--danger");
    // }
    // setTimeout(function () {
    //     $('#mw-notification').hide();
    // }, 5000);
    
    swal.fire({
        html: stext,
        icon: type,
        buttonsStyling: false,
        confirmButtonText: "OK",
        customClass: {
			confirmButton: "btn font-weight-bold btn-light-primary"
		}
    }).then(function() {
		
	});
}
function refresh_topbar(){
    $.ajax({
        url: HOST_URL+"/refresh/topbar",
        type: "get",
        beforeSend: function () {
            block_ui();
        },
        success:function(tophtml){
            $('#kt_header').html(tophtml);
          unblock_ui();
        }
      });
}
function toast(type,message){
    toastr.options = {
        "closeButton"      : true,
        "debug"            : false,
        "newestOnTop"      : false,
        "progressBar"      : false,
        "positionClass"    : "toast-top-center",
        "preventDuplicates": false,
        "onclick"          : null,
        "showDuration"     : "500",
        "hideDuration"     : "1000",
        "timeOut"          : "5000",
        "extendedTimeOut"  : "1000",
        "showEasing"       : "linear",
        "hideEasing"       : "linear",
        "showMethod"       : "fadeIn",
        "hideMethod"       : "fadeOut"
      };
      if (type=="success"){
        toastr.success(message);
      }
      if (type=="info"){
        toastr.info(message);
      }
      if (type=="warning"){
        toastr.warning(message);
      }
      if (type=="error"){
        toastr.error(message);
      }
      
}