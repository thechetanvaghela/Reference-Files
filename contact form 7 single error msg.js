/**
   * Disable WPCF7 button while it's submitting
   * Stops duplicate enquiries coming through
   */
  document.addEventListener( 'wpcf7submit', function( event ) {
      // find only disbaled submit buttons
      var button = $('.wpcf7-submit[disabled]');
      // grab the old value
      var old_value = button.attr('data-value');
      // enable the button
      button.prop('disabled', false);
      // put the old value back in
      button.val(old_value);
  }, false );

  jQuery('form.wpcf7-form').on('submit',function() {
      var form = $(this);
      var button = form.find('input[type=submit]');
      var current_val = button.val();
      // store the current value so we can reset it later
      button.attr('data-value', current_val);
      // disable the button
      button.prop("disabled", true);
      // tell the user what's happening
      //button.val("Sending, please wait...");
  });




  /* OR */

  /* contact form for single time display error message */
    jQuery( '.wpcf7-form' ).submit(function() { // form class name
        jQuery( '.wpcf7-submit' ).attr( 'disabled', true );
    });

    document.addEventListener( 'wpcf7invalid', function() {
        jQuery( '.wpcf7-submit' ).attr( 'disabled', false );
    }, false );

    document.addEventListener( 'wpcf7mailsent', function( event ) {
        jQuery( '.wpcf7-submit' ).attr( 'disabled', false );
    }, false );
    /* /contact form for single time display error message */