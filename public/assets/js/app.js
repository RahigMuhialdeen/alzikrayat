/**
 * Initializes client-side form validation and gallery layout controls.
 *
 * HTML5 validation, this custom JavaScript layer, and PHP validation work
 * together so invalid data cannot rely on only one validation mechanism.
 *
 * @returns {void} Does not return a value.
 */
(() => {
  'use strict';

  /**
   * Finds Bootstrap validation feedback inside a form field container.
   *
   * @param {HTMLElement} field Input or textarea being validated.
   * @returns {HTMLElement|null} Matching feedback element, when available.
   */
  const getFeedback = field =>
    field.parentElement?.querySelector('.invalid-feedback') ?? null;

  /**
   * Sets a custom validation message and updates visible Bootstrap feedback.
   *
   * @param {HTMLInputElement|HTMLTextAreaElement} field Field being validated.
   * @param {string} message Message to display.
   * @returns {void} Does not return a value.
   */
  const setValidationError = (field, message) => {
    field.setCustomValidity(message);

    const feedback = getFeedback(field);

    if (feedback) {
      feedback.textContent = message;
    }
  };

  /**
   * Clears a field's custom validation message.
   *
   * @param {HTMLInputElement|HTMLTextAreaElement} field Field to reset.
   * @returns {void} Does not return a value.
   */
  const clearValidationError = field => {
    field.setCustomValidity('');
  };

  /**
   * Initializes custom validation for all forms using the needs-validation class.
   *
   * The field checks are conditional because the application contains different
   * forms for registration, login, upload, and comments.
   *
   * @returns {void} Does not return a value.
   */
  document.querySelectorAll('.needs-validation').forEach(
    /**
     * Configures validation behavior for one application form.
     *
     * The callback attaches the submit validation handler and the appropriate
     * input/change handlers to the form's fields.
     *
     * @param {HTMLFormElement} form Form whose validation behavior is initialized.
     * @returns {void} Does not return a value.
     */
    form => {
      /**
       * Validates the current form before submission.
       *
       * Both native HTML5 validation and the application's custom JavaScript
       * validation rules are evaluated. Submission is cancelled whenever either
       * validation layer reports invalid data.
       *
       * @param {SubmitEvent} event Browser submit event associated with the form.
       * @returns {void} Does not return a value.
       */
      form.addEventListener('submit', event => {
        let isValid = true;

        const firstName = form.querySelector('#first_name');
        const lastName = form.querySelector('#last_name');
        const email = form.querySelector('#email');
        const password = form.querySelector('#password');
        const title = form.querySelector('#title');
        const photo = form.querySelector('#photo');
        const comment = form.querySelector('#comment');

        // Register form: first and last names must contain letters only.
        if (firstName) {
          if (firstName.value.trim() === '') {
            setValidationError(firstName, 'First name is required.');
            isValid = false;
          } else if (!/^[A-Za-z]+$/.test(firstName.value.trim())) {
            setValidationError(firstName, 'First name must contain letters only.');
            isValid = false;
          } else {
            clearValidationError(firstName);
          }
        }

        if (lastName) {
          if (lastName.value.trim() === '') {
            setValidationError(lastName, 'Last name is required.');
            isValid = false;
          } else if (!/^[A-Za-z]+$/.test(lastName.value.trim())) {
            setValidationError(lastName, 'Last name must contain letters only.');
            isValid = false;
          } else {
            clearValidationError(lastName);
          }
        }

        // Login/register forms: use the browser email validator plus a custom message.
        if (email) {
          if (email.value.trim() === '') {
            setValidationError(email, 'Email is required.');
            isValid = false;
          } else if (!email.validity.valid) {
            setValidationError(email, 'Please enter a valid email address.');
            isValid = false;
          } else {
            clearValidationError(email);
          }
        }

        if (password) {
          if (password.value.trim() === '') {
            setValidationError(password, 'Password is required.');
            isValid = false;
          } else if (password.value.length < 8) {
            setValidationError(password, 'Password must be at least 8 characters.');
            isValid = false;
          } else {
            clearValidationError(password);
          }
        }

        // Upload form: title and image are validated before the request reaches PHP.
        if (title) {
          if (title.value.trim() === '') {
            setValidationError(title, 'A title is required.');
            isValid = false;
          } else if (title.value.length > 200) {
            setValidationError(title, 'Title must be at most 200 characters.');
            isValid = false;
          } else {
            clearValidationError(title);
          }
        }

        if (photo) {
          const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
          const selectedFile = photo.files[0];

          if (!selectedFile) {
            setValidationError(photo, 'Choose an image.');
            isValid = false;
          } else if (!allowedTypes.includes(selectedFile.type)) {
            setValidationError(photo, 'Only JPG, JPEG, PNG, and WEBP images are allowed.');
            isValid = false;
          } else if (selectedFile.size > 5 * 1024 * 1024) {
            setValidationError(photo, 'Image size must not exceed 5 MB.');
            isValid = false;
          } else {
            clearValidationError(photo);
          }
        }

        /**
         * Applies the maxlength validation rule to one text field.
         *
         * This callback reads the HTML maxlength attribute and compares it
         * with the current field value so the JavaScript validation layer
         * explicitly enforces the declared maximum length.
         *
         * @param {HTMLInputElement|HTMLTextAreaElement} field Text field being checked.
         * @returns {void} Does not return a value.
         */
        form.querySelectorAll('input[maxlength], textarea[maxlength]').forEach(field => {
          const maximum = Number(field.getAttribute('maxlength'));

          if (maximum > 0 && field.value.length > maximum) {
            setValidationError(
              field,
              `This field must be at most ${maximum} characters.`
            );
            isValid = false;
          }
        });

        // Comment form: mirror the required/maximum-length rules enforced by PHP.
        if (comment) {
          if (comment.value.trim() === '') {
            setValidationError(comment, 'Comment is required.');
            isValid = false;
          } else if (comment.value.length > 1000) {
            setValidationError(comment, 'Comment must be at most 1000 characters.');
            isValid = false;
          } else {
            clearValidationError(comment);
          }
        }

        // Submit only when both native HTML5 validation and custom JS validation pass.
        if (!form.checkValidity() || !isValid) {
          event.preventDefault();
          event.stopPropagation();
        }

        form.classList.add('was-validated');
      });

      /**
       * Attaches a validation-reset handler to one form field.
       *
       * File inputs use the change event because their selected value changes
       * through file selection, while text-based controls use input so their
       * custom validation state can be cleared while the user edits the value.
       *
       * @param {HTMLInputElement|HTMLTextAreaElement} field Field receiving the event handler.
       * @returns {void} Does not return a value.
       */
      form.querySelectorAll('input, textarea').forEach(field => {
        const eventName = field.type === 'file' ? 'change' : 'input';

        /**
         * Clears the field's custom validation state after user input.
         *
         * @returns {void} Does not return a value.
         */
        field.addEventListener(eventName, () => clearValidationError(field));
      });
    }
  );

  /**
   * Initializes the gallery layout selector.
   *
   * Users can switch between three-column, four-column, and list layouts
   * without reloading the page.
   *
   * @returns {void} Does not return a value.
   */
  const gallery = document.getElementById('gallery');
  const layoutButtons = document.querySelectorAll('[data-layout]');

  if (gallery && layoutButtons.length) {
    /**
     * Attaches the layout-selection behavior to one gallery layout button.
     *
     * @param {HTMLElement} button Button containing the requested layout
     *                             in its data-layout attribute.
     * @returns {void} Does not return a value.
     */
    layoutButtons.forEach(button => {
      /**
       * Changes the gallery CSS layout when the user selects this button.
       *
       * The handler removes the previous layout classes, marks the selected
       * button as active, and adds the CSS class corresponding to its data value.
       *
       * @returns {void} Does not return a value.
       */
      button.addEventListener('click', () => {
        // Keep exactly one layout button visually active.
        layoutButtons.forEach(
          /**
           * Removes the active state from one layout button.
           *
           * @param {HTMLElement} item Layout button being reset.
           * @returns {void} Does not return a value.
           */
          item => {
            item.classList.remove('active');
          }
        );

        button.classList.add('active');

        gallery.classList.remove(
          'gallery-grid-3',
          'gallery-grid-4',
          'gallery-list'
        );

        // Convert the selected button's data value into the matching CSS class.
        gallery.classList.add(
          button.dataset.layout === 'grid4'
            ? 'gallery-grid-4'
            : button.dataset.layout === 'list'
              ? 'gallery-list'
              : 'gallery-grid-3'
        );
      });
    });
  }
})();
