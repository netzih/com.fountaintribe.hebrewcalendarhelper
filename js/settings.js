(function($, _) {
  // Suggest Hebcal places under the "City or ZIP code" field as the admin types.
  $(function() {
    var $input = $('input[name="hebrewcalendarhelper_shabbat_location"]');
    if (!$input.length) {
      return;
    }
    var $list = $('<datalist id="hebrewcalendarhelper-places"></datalist>').insertAfter($input);
    var timer, last = '';
    $input.on('input', function() {
      var q = $.trim($input.val());
      clearTimeout(timer);
      if (q.length < 2 || q === last) {
        return;
      }
      timer = setTimeout(function() {
        last = q;
        $.getJSON(CRM.vars.hebrewcalendarhelper.placesUrl, {q: q}).done(function(places) {
          $list.empty();
          _.each(places, function(place) {
            $('<option>').attr('value', place).appendTo($list);
          });
        });
      }, 250);
    });
  });
})(CRM.$, CRM._);

(function($) {
  // "Preview another week" on the settings page reloads it with ?preview=YYYY-MM-DD.
  $(document).on('click', '#hebrewcalendarhelper-preview-go', function(e) {
    e.preventDefault();
    var date = $('#hebrewcalendarhelper-preview').val();
    if (date) {
      window.location = CRM.url('civicrm/admin/setting/hebrewcalendarhelper', {reset: 1, preview: date});
    }
  });
})(CRM.$);

(function($) {
  // The preview box is added at the top of the Shabbat section; put it under the heading.
  $(function() {
    var $box = $('#hebrewcalendarhelper-shabbat-preview');
    var $anchor = $box.closest('.crm-setting-section').find('> h3, > .description').last();
    if ($anchor.length) {
      $box.insertAfter($anchor);
    }
  });
})(CRM.$);
