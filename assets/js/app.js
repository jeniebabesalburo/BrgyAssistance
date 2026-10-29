const toggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
if (toggle && sidebar) {
  toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
}
document.querySelectorAll('[data-confirm]').forEach((element) => {
  element.addEventListener('click', (event) => {
    const message = element.getAttribute('data-confirm') || 'Are you sure?';
    if (!window.confirm(message)) event.preventDefault();
  });
});
