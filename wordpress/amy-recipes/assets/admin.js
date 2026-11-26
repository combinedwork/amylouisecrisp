/* Photo picker for collections, and the festive season quick-start buttons. */
jQuery(function ($) {
  $(document).on('click', '.alc-image-choose', function (e) {
    e.preventDefault();
    var box = $(this).closest('.alc-image-field');
    var frame = wp.media({ title: 'Choose a photo', button: { text: 'Use this photo' }, library: { type: 'image' }, multiple: false });
    frame.on('select', function () {
      var a = frame.state().get('selection').first().toJSON();
      var url = (a.sizes && (a.sizes.medium || a.sizes.full)) ? (a.sizes.medium || a.sizes.full).url : a.url;
      box.find('input[name="alc_image"]').val(a.id);
      box.find('.alc-image-preview').attr('src', url).prop('hidden', false);
      box.find('.alc-image-choose').text('Change photo');
      box.find('.alc-image-remove').prop('hidden', false);
    });
    frame.open();
  });
  $(document).on('click', '.alc-image-remove', function (e) {
    e.preventDefault();
    var box = $(this).closest('.alc-image-field');
    box.find('input[name="alc_image"]').val('');
    box.find('.alc-image-preview').attr('src', '').prop('hidden', true);
    box.find('.alc-image-choose').text('Choose photo');
    $(this).prop('hidden', true);
  });
  // WordPress clears the add-collection form after saving; reset the photo too
  $(document).ajaxComplete(function (ev, xhr, s) {
    if (s && s.data && String(s.data).indexOf('action=add-tag') > -1) { $('.alc-image-remove').trigger('click'); }
  });
  $(document).on('click', '.alc-preset', function () {
    var b = $(this);
    $('#season_name').val(b.data('name')); $('#season_emoji').val(b.data('emoji'));
    $('#season_heading').val(b.data('heading')); $('#season_text').val(b.data('text'));
  });
});
