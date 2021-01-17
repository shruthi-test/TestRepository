
    <!-- footer -->
    <div class="ftr">
      <div class="container">
          <div class="col-md-6">
            <h3>Working Hours</h3>
            <p class="ftr-hr"><strong>Mon – Fri</strong><br>
            9:00 am – 4:30 pm<br>
            <em>* Children are often free for lunch 1.00 to 1.30</em><br>
            <strong>Saturday</strong><br>
            9:00 am – 12:30 pm<br>
            <strong>Sunday</strong><br>
            Holiday</p>
          </div>
           <div class="col-md-6">
            <h3>About Us</h3>
            <p>Choosing the right school for your child is important as it can have a big impact on their happiness and well being as well as how well they do at school. We hope you will find this website helpful, informative and stimulating towards making the right decision for our child. Education plays the most important role in acquiring professional and social skills and a positive attitude to face the challenges of life.</p>
          </div>
          <div class="clearfix"></div>
          <div class="ftr-btm">
          <p> Copyright © All rights reserved.</p>
        </div>
        </div>
      </div>
        
      
<!-- active -->
<script type="text/javascript">

   $(document).ready(function () {
     var url = window.location;
    // for sidebar menu entirely but not cover treeview
    $('.nav a').filter(function() {
        return this.href == url;
    }).addClass('active');

});
</script>

<!-- dropdown hover -->
<script type="text/javascript">
  $(document).ready(function () {
$('.navbar .dropdown').hover(function () {
        $(this).find('.dropdown-menu').first().stop(true, true).slideDown(150);
    }, function () {
        $(this).find('.dropdown-menu').first().stop(true, true).slideUp(105)
    });
});

$('.dropdown-toggle').click(function(e) {
  if ($(document).width() > 768) {
    e.preventDefault();

    var url = $(this).attr('href');

       
    if (url !== '#') {
    
      window.location.href = url;
    }

  }
});
</script>


    <!-- Smooth Scroll for prdoucts -->

    <script type="text/javascript">

    $(document).ready(function() {
      // Add smooth scrolling to all links
      $(".main-menu a").on('click', function(event) {

        // Make sure this.hash has a value before overriding default behavior
        if (this.hash !== "") {

          // Store hash
          var hash = this.hash;

          // Using jQuery's animate() method to add smooth page scroll
          // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
          $('html, body').animate({
            scrollTop: $(hash).offset().top
          }, 2000, function() {

            // Add hash (#) to URL when done scrolling (default click behavior)
            window.location.hash = hash;
          });
          return false;
        } // End if
      });
    });

    </script>
    <!-- counters -->
      <script type="text/javascript">
      // number count for stats, using jQuery animate

      $('.counting').each(function() {
        var $this = $(this),
            countTo = $this.attr('data-count');
        
        $({ countNum: $this.text()}).animate({
          countNum: countTo
        },

        {

          duration: 3000,
          easing:'linear',
          step: function() {
            $this.text(Math.floor(this.countNum));
          },
          complete: function() {
            $this.text(this.countNum);
            //alert('finished');
          }

        });  
        

      });
    </script>

    <!-- testimonials-->
    <script type="text/javascript">

        $('.testi-slide').owlCarousel({
            loop:true,
            nav: false,
            rewind:true,
            autoplay:true,
            dots:true,
            autoplayHoverPause:true,
            margin:20,
            responsiveClass:true,
            responsive:{
                0:{
                    items:1,
                    margin:10,
                    nav:false
                },
                600:{
                    items:1,
                    nav:false
                },
                1000:{
                    items:1,
                    nav:false
                }
            }
        });
    </script>
    <!-- news-->
    <script type="text/javascript">

        $('.news-slide').owlCarousel({
            loop:true,
            nav: false,
            rewind:true,
            autoplay:true,
            dots:false,
            autoplayHoverPause:true,
            margin:30,
            responsiveClass:true,
            responsive:{
                0:{
                    items:1,
                    margin:10,
                    nav:false
                },
                600:{
                    items:3,
                    nav:false
                },
                1000:{
                    items:3,
                    nav:false
                }
            }
        });
    </script>
     <!-- galry-->
    <script type="text/javascript">

        $('.gal-slide').owlCarousel({
            loop:true,
            nav: false,
            rewind:true,
            autoplay:true,
            dots:false,
            autoplayHoverPause:true,
            margin:15,
            responsiveClass:true,
            responsive:{
                0:{
                    items:1,
                    margin:10,
                    nav:false
                },
                600:{
                    items:3,
                    nav:false
                },
                1000:{
                    items:3,
                    nav:false
                }
            }
        });
    </script>
  </body>
</html>




