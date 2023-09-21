
document.addEventListener('DOMContentLoaded', function() {
    const link = document.getElementById('btn-clear');
    const confirmBtn = document.getElementById('btn-confirm');
    console.log(confirmBtn);
    console.log(link);
    link.addEventListener('click', function(event) {
      event.preventDefault();
      const href = this.getAttribute('href');
      confirmBtn.setAttribute('href', href);
      
      const modal = new bootstrap.Modal(document.getElementById('confirm-modal'));
      modal.show();
    });
  });
  