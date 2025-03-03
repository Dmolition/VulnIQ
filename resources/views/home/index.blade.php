<!DOCTYPE html>
<html lang="en">

  <head>
    <title>CyberAttack</title>

@include('home.css')
  </head>

<body>


  <!-- ***** Header Area Start ***** -->
  <header class="header-area header-sticky">
@include('home.header')
  </header>
  <!-- ***** Header Area End ***** -->

  <!-- ***** Main Banner Area Start ***** -->
@include('home.slider')
  <!-- ***** Main Banner Area End ***** -->

  <!-- ***** Services Section Start ***** -->
@include('home.services')
 <!-- ***** Services Section End ***** -->

<!-- ***** Simple Cta Start***** -->
@include('home.cta')
<!-- ***** Simple Cta End ***** -->

<!-- ***** About us Start ***** -->
@include('home.about')
   <!-- ***** About us End ***** -->

    <!-- ***** Calculator Start ***** -->

@include('home.calculator')
   <!-- ***** Calculator End ***** -->

    <!-- ***** Testimonials Start ***** -->

  @include('home.testimonial')

   <!-- ***** Testimonial End ***** -->

    <!-- ***** Partners Start ***** -->
@include('home.partners')

   <!-- ***** Partners End ***** -->

    <!-- *****  Footer ***** -->
  @include('home.footer')
  </body>
</html>