(() => {
  const toggle = document.getElementById('mobileToggle');
  const sidebar = document.getElementById('sidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    document.addEventListener('click', (event) => {
      if (window.innerWidth <= 760 && sidebar.classList.contains('open') && !sidebar.contains(event.target) && !toggle.contains(event.target)) sidebar.classList.remove('open');
    });
  }
})();
