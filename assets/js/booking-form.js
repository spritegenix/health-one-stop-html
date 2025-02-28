$(function () {
  // Get the form.
  var form = $('#booking-form');

  // Get the messages div.
  var formMessages = $('.ajax-response');

  // Set up an event listener for the contact form.
  $(form).submit(function (e) {
    // Stop the browser from submitting the form.
    e.preventDefault();

    // Serialize the form data.
    var formData = $(form).serialize();

    // Change button text to indicate submission is in progress.
    $('#booking-button').text('Submitting...');

    // Submit the form using AJAX.
    $.ajax({
      type: 'POST',
      url: $(form).attr('action'),
      data: formData,
    })
      .done(function (response) {
        // Make sure that the formMessages div has the 'success' class.
        $(formMessages).removeClass('error').addClass('success');

        // Set the message text.
        $(formMessages).text(response);

        // Clear the form fields.
        $('#booking-form input, #booking-form textarea').val('');

        // Change button text to "Sent!" and reset after 3 seconds.
        $('#booking-button').text('Sent!');
        setTimeout(() => {
          $('#booking-button').text('Submit');
        }, 3000);
      })
      .fail(function (data) {
        // Make sure that the formMessages div has the 'error' class.
        $(formMessages).removeClass('success').addClass('error');

        // Set the message text.
        if (data.responseText !== '') {
          $(formMessages).text(data.responseText);
        } else {
          $(formMessages).text(
            'Oops! An error occurred and your message could not be sent.'
          );
        }

        // Reset button text after 3 seconds.
        setTimeout(() => {
          $('#booking-button').text('Submit');
        }, 3000);
      });
  });
});
