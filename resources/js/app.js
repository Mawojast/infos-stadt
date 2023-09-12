import './bootstrap';
import $ from "jquery";
window.$ = $;;



    // Überprüfen, ob eine vertikale Scrollbar vorhanden ist
function hasVerticalScrollbar() {
  // Gesamthöhe des Dokuments (einschließlich nicht sichtbarer Bereiche)
  const documentHeight = Math.max(
    document.body.scrollHeight,
    document.documentElement.scrollHeight,
    document.body.offsetHeight,
    document.documentElement.offsetHeight,
    document.body.clientHeight,
    document.documentElement.clientHeight
  );

  // Höhe des sichtbaren Bereichs (Viewport)
  const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

  // Überprüfen, ob die Gesamthöhe des Dokuments größer als die Höhe des sichtbaren Bereichs ist
  return documentHeight > viewportHeight;
}

// Beispielaufruf
if (!hasVerticalScrollbar()) {
    $('div.footer').addClass('fixed-bottom');

}

/////////////////////////////////////////////////////////////////////

$(document).ready(function() {
    // Show or hide the button based on scrolling
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

///////////////////////////////////////////////////////////////////////////

$("#search-city-form").on("submit", function(event) {
    //alert("Handler for `submit` called.");
    //event.preventDefault();

    // Create the element
    let element = document.createElement("div");

    // Set the element's style
    element.style.width = "100px";
    element.style.height = "100px";
    element.style.backgroundColor = "#0b0d0f;";
    element.style.position = "absolute";
    element.style.top = "50%";
    element.style.left = "50%";
    element.style.transform = "translate(-50%, -50%) rotate(45deg)";

    // Append the element to the document
    document.body.appendChild(element);
})
