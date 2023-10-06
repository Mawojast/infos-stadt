/**
 * attaches footer to bottom
 */
function hasVerticalScrollbar() {

    // Total height of the document
    const documentHeight = Math.max(
        document.body.scrollHeight,
        document.documentElement.scrollHeight,
        document.body.offsetHeight,
        document.documentElement.offsetHeight,
        document.body.clientHeight,
        document.documentElement.clientHeight
    );
    const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    return documentHeight > viewportHeight;
}

if (!hasVerticalScrollbar()) {
    $('div.footer').addClass('fixed-bottom');
}

/**
 * Show or hide scroll up button
 */
$(document).ready(function() {

    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('#scroll-to-top').fadeIn();
        } else {
            $('#scroll-to-top').fadeOut();
        }
    });

    // Scroll to top on button click
    $('#scroll-to-top').click(function() {
        $('html, body').animate({scrollTop : 0}, 10);
        return false;
    });
});


/**
 * Loading spinner
 */
$(document).ready(function() {

    //$('.spinner-box').addClass('m-0');
    $('.spinner-box').removeClass('m-5');
    $(".spinner-box").css("display", "none");
    $("#search-city-form").on("submit", function(event){
        $(".spinner-grow").css("display", "block");
        $(".search-button-image").css("display", "none")
        setTimeout(function() {
            $(".spinner-grow").css("display", "none");
            $(".search-button-image").css("display", "block")
        }, 5000);
    })
});
