;(function($, window) {

  'use strict';

  window.Moria = window.Moria || {
    init: function() {
      this.mellon();
    },
    mellon: function() {
      $(".search-read-more").on('click', function(e) {
        
        e.preventDefault();

        var $this = $(this);
        var $content = $this.parent();

        $.ajax({
          url: durin.ajaxurl,
          method: 'post',
          async: true,
          cache: false,
          dataType: 'html',
          data: {
            'action': 'moria_enter',
            'post_id': $this.data('post-id'),
            'nonce': durin.nonce,
          },
          success: function(res) {
            // Create a container to fade in the new content
            var $new_container = $('<div />');

            // Add the response to the container
            $new_container.html( res );

            // Fade the content out, add the new content and fade it in
            $content.fadeOut('400', function(){
              $content
                .after( $new_container.fadeIn() )
                .remove();
            });
          }
        });
      });
    }
  };

  Moria.init();

})(jQuery, window);
