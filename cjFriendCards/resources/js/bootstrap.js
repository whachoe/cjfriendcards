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