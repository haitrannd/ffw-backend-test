(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.nodeCounter = {
    attach: function (context, settings) {
      if (once('nodeCounter', 'body').length) {
        function getCsrfToken(callback) {
          $
            .get(Drupal.url('session/token'))
            .done(function (data) {
              var csrfToken = data;
              callback(csrfToken);
            });
        }

        function nodeCounter(csrfToken) {
          $.ajax({
            type: 'POST',
            cache: false,
            url: drupalSettings.site_settings.url + '?_format=json',
            contentType: "application/json",
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-Token': csrfToken,
            },
          });
        }

        getCsrfToken(function (csrfToken) {
          nodeCounter(csrfToken);
        });
      }
    }
  };
})(jQuery, Drupal, drupalSettings, once);
