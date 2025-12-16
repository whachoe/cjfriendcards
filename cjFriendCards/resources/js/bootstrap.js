import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

(function() {
  if (document.getElementById('first_name') && document.getElementById('last_name') && document.getElementById('unique_name')) {
    document.addEventListener('DOMContentLoaded', function() {
          const firstNameInput = document.getElementById('first_name');
          const lastNameInput = document.getElementById('last_name');
          const uniqueNameInput = document.getElementById('unique_name');

          function generateUniqueName() {
              const firstName = firstNameInput.value.toLowerCase().trim().replaceAll(' ','-');
              const lastName = lastNameInput.value.toLowerCase().trim().replaceAll(' ','-');
              
              if (firstName && lastName) {
                  uniqueNameInput.value = `${firstName}-${lastName}`;
              } else {
                  uniqueNameInput.value = '';
              }
          }

          firstNameInput.addEventListener('input', generateUniqueName);
          lastNameInput.addEventListener('input', generateUniqueName);
      });
    }
})();

// Birthday date format conversion (dd-mm-yyyy to yyyy-mm-dd)
(function() {
  if (document.getElementById('birthday')) {
    document.addEventListener('DOMContentLoaded', function() {
      const birthdayInput = document.getElementById('birthday');
      const birthdayHidden = document.getElementById('birthday_hidden');

      if (birthdayInput && birthdayHidden) {
        birthdayInput.addEventListener('blur', function() {
          const value = this.value.trim();
          if (value) {
            const parts = value.split('-');
            if (parts.length === 3) {
              const day = parts[0];
              const month = parts[1];
              const year = parts[2];
              const isoDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
              birthdayHidden.value = isoDate;
            }
          }
        });
      }
    });
  }
})();