(() => {
  const canvas = document.getElementById('costChart');
  if (!canvas || typeof Chart === 'undefined') return;
  const rows = Array.isArray(window.eoqChartRows) ? window.eoqChartRows : [];
  const labels = rows.map((row) => {
    const [year, month] = String(row.month_key).split('-').map(Number);
    return new Intl.DateTimeFormat('id-ID', { month: 'short', year: 'numeric' }).format(new Date(year, month - 1, 1));
  });
  new Chart(canvas, {
    type: 'line',
    data: { labels, datasets: [
      { label: 'Biaya pemesanan', data: rows.map(row => Number(row.ordering_cost || 0)), borderColor: '#1f5d87', backgroundColor: 'rgba(31,93,135,.08)', borderWidth: 2, pointRadius: 3, tension: .25 },
      { label: 'Biaya penyimpanan', data: rows.map(row => Number(row.holding_cost || 0)), borderColor: '#a77932', backgroundColor: 'rgba(167,121,50,.06)', borderWidth: 2, pointRadius: 3, tension: .25 }
    ] },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 13 } } },
        tooltip: { callbacks: { label: context => context.dataset.label + ': ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y || 0) } } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 12 } } },
        y: { beginAtZero: true, ticks: { font: { size: 12 }, callback: value => new Intl.NumberFormat('id-ID', { notation: 'compact', maximumFractionDigits: 1 }).format(value) } }
      }
    }
  });
})();
