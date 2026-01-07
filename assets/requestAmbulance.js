
//Just confirm before submit.
document.addEventListener('DOMContentLoaded', function () {
  var form = document.querySelector('form[method="POST"]');
  if (!form) return;
  form.addEventListener('submit', function (e) {
    var ok = confirm("Submit ambulance request now?");
    if (!ok) e.preventDefault();
  });
});
