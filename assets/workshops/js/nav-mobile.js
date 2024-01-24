$(function(){

    // Returns width of browser viewport
    var browser = $(window).width();
    // Returns width of HTML document
    var document = $(document).width();

    $('.m-nav .toggle-nav').click(function(e){
        $('.m-nav .nav-mobile').addClass('style-mobile').slideToggle('slow');
        e.preventDefault();
    });

});
