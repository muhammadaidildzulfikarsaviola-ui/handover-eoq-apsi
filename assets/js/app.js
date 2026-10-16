(() => {
  const icons = {
    'grid-2x2': '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
    package: '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="M3 8v9l9 5 9-5V8M12 13v9"/>',
    calculator: '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M8 7h8M8 11h2m4 0h2M8 15h2m4 0h2M8 18h2m4 0h2"/>',
    history: '<path d="M3 12a9 9 0 1 0 2.6-6.4L3 8"/><path d="M3 3v5h5M12 7v5l3 2"/>',
    'chart-no-axes-combined': '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-5 5"/>',
    'book-open': '<path d="M12 7v14M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z"/>',
    'graduation-cap': '<path d="m2 10 10-5 10 5-10 5-10-5Z"/><path d="M6 12v5c3 3 9 3 12 0v-5m4-2v6"/>',
    ellipsis: '<circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/>',
    menu: '<path d="M4 6h16M4 12h16M4 18h16"/>',
    'circle-help': '<circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3m.1 4h.01"/>',
    'calendar-days': '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18M8 15h.01M12 15h.01M16 15h.01M8 18h.01M12 18h.01"/>',
    plus: '<path d="M12 5v14M5 12h14"/>',
    wallet: '<rect x="3" y="5" width="18" height="15" rx="2"/><path d="M3 9h18M16 15h.01M3 5l14-2"/>',
    'calendar-clock': '<path d="M8 2v4m8-4v4M3 10h13M5 4h14a2 2 0 0 1 2 2v6"/><circle cx="16" cy="17" r="5"/><path d="M16 14v3l2 1"/>',
    'package-plus': '<path d="m12 3 9 5-9 5-9-5 9-5ZM3 8v9l9 5 9-5V8M12 13v9"/><path d="M18 3v6m-3-3h6"/>',
    'chevron-right': '<path d="m9 18 6-6-6-6"/>',
    'file-chart-column': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 18v-4m4 4V10m4 8v-6"/>',
    'arrow-right': '<path d="M5 12h14m-7-7 7 7-7 7"/>',
    'arrow-up-right': '<path d="M7 17 17 7M7 7h10v10"/>',
    database: '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.7 4 3 9 3s9-1.3 9-3V5M3 12c0 1.7 4 3 9 3s9-1.3 9-3"/>',
    'file-clock': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h7"/><path d="M14 2v6h6M14 2l6 6"/><circle cx="18" cy="17" r="4"/><path d="M18 15v2l1.5 1"/>',
    'arrow-down': '<path d="M12 5v14m-7-7 7 7 7-7"/>'
  };
  document.querySelectorAll('[data-icon]').forEach((node) => {
    const name = node.getAttribute('data-icon');
    if (!icons[name]) return;
    node.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${icons[name]}</svg>`;
  });
  const sidebar = document.getElementById('sidebar');
  const mobileMenu = document.getElementById('mobileMenu');
  if (sidebar && mobileMenu) {
    mobileMenu.addEventListener('click', () => sidebar.classList.toggle('open'));
    document.addEventListener('click', (event) => {
      if (window.innerWidth <= 800 && sidebar.classList.contains('open') && !sidebar.contains(event.target) && !mobileMenu.contains(event.target)) sidebar.classList.remove('open');
    });
  }
})();
