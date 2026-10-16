(() => {
  const canvas = document.getElementById('costChart');
  if (!canvas || typeof Chart === 'undefined') return;
  const rows = Array.isArray(window.eoqChartRows) ? window.eoqChartRows : [];
  const monthLabel = (key) => {
    const [year, month] = String(key).split('-').map(Number);
    if (!year || !month) return String(key);
    return new Intl.DateTimeFormat('id-ID', { month: 'short', year: '2-digit' }).format(new Date(year, month - 1, 1));
  };
  new Chart(canvas, {
    type: 'line',
    data: {
      labels: rows.map((row) => monthLabel(row.month_key)),
      datasets: [
        { label: 'Biaya pemesanan', data: rows.map((row) => Number(row.ordering_cost || 0)), borderColor: '#4d8d68', backgroundColor: 'rgba(77,141,104,.08)', borderWidth: 2, pointRadius: 3, pointHoverRadius: 5, pointBackgroundColor: '#4d8d68', tension: .32, fill: true },
        { label: 'Biaya penyimpanan', data: rows.map((row) => Number(row.holding_cost || 0)), borderColor: '#cfad6b', backgroundColor: 'rgba(207,173,107,.05)', borderWidth: 2, pointRadius: 3, pointHoverRadius: 5, pointBackgroundColor: '#cfad6b', tension: .32, fill: false }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: (context) => `${context.dataset.label}: ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y || 0)}` } }
      },
      scales: {
        x: { grid: { display: false }, border: { display: false }, ticks: { color: '#8b968d', font: { family: 'DM Sans', size: 10 } } },
        y: { beginAtZero: true, border: { display: false, dash: [3, 4] }, grid: { color: '#edf1ed', drawTicks: false }, ticks: { color: '#8b968d', padding: 9, font: { family: 'DM Sans', size: 9 }, callback: (value) => new Intl.NumberFormat('id-ID', { notation: 'compact', maximumFractionDigits: 1 }).format(value) } }
      }
    }
  });
})();
