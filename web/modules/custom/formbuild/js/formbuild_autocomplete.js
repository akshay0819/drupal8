(function ($) {
  Drupal.behaviors.autocomplete.attach = function attach(context) {
    var $autocomplete = $(context).find('input.form-autocomplete,textarea.form-autocomplete').once('autocomplete');
    if ($autocomplete.length) {
      var blacklist = $autocomplete.attr('data-autocomplete-first-character-blacklist');
      $.extend(Drupal.autocomplete.options, {
        firstCharacterBlacklist: blacklist || ''
      });

      $autocomplete.autocomplete(Drupal.autocomplete.options).each(function () {
        $(this).data('ui-autocomplete')._renderItem = Drupal.autocomplete.options.renderItem;
      });

      $autocomplete.on('compositionstart.autocomplete', function () {
        Drupal.autocomplete.options.isComposing = true;
      });
      $autocomplete.on('compositionend.autocomplete', function () {
        Drupal.autocomplete.options.isComposing = false;
      });
    }
  };
})(jQuery);
