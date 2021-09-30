jQuery(document).ready(function($) {
    $(".poup_model").on('click', function(e) {
      console.log(e);
        $('.add_campaign').toggleClass('is-visible');
      });
})