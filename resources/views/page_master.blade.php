<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('/info-1.png')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="{{asset('/css/app.css')}}" rel="stylesheet">
    <title>@yield('title')</title>
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
</head>
<body @yield('background') style="object-fit: contain;">
        @include('header')
        @yield('main')
        @include('footer')
        <script
  src="https://code.jquery.com/jquery-3.7.1.js"
  integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
  crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <script src="{{asset('/js/app.js')}}"></script>



        <script>

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

//   $("#search-city-form").on("submit", function(event) {

//       //event.preventDefault();

//       // Create the element
//       let element = document.createElement("div");

//       // Set the element's style
//       element.style.width = "100px";
//       element.style.height = "100px";
//       element.style.backgroundColor = "#ff0000;";
//       element.style.position = "absolute";
//       element.style.top = "50%";
//       element.style.left = "50%";
//       element.style.transform = "translate(-50%, -50%) rotate(45deg)";

//       // Append the element to the document
//       document.body.appendChild(element);
//   })

$("#city-input").on("keyup", function(event) {
    if (event.key === "Enter") {
        event.preventDefault(); // Verhindert das Standardverhalten des Formulars (Seitenneuladen)

        // Fügen Sie hier Ihren Code zum Ausführen des Query-Codes ein.

        alert("Enter-Taste wurde gedrückt.");
    }
});

        </script>
</body>
</html>
