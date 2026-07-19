// =====================================================
// PEMASUKAN-TAMPIL.JS — Chart Pemasukan 7 Hari
// Requires: chartLabels & chartData variables defined inline
// =====================================================

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartPemasukan').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(34, 197, 94, 0.3)');
    gradient.addColorStop(1, 'rgba(34, 197, 94, 0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Pemasukan (Rp)',
                data: chartData,
                borderColor: '#22c55e',
                backgroundColor: gradient,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#22c55e',
                pointBorderColor: '#1c1f2e',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1c1f2e',
                    titleColor: '#f1f5f9',
                    bodyColor: '#94a3b8',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(ctx) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 11, family: 'Inter' } }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: {
                        color: '#64748b',
                        font: { size: 11, family: 'Inter' },
                        callback: function(v) {
                            if (v >= 1000000) return (v/1000000).toFixed(1) + ' Jt';
                            if (v >= 1000) return (v/1000).toFixed(0) + ' Rb';
                            return v;
                        }
                    }
                }
            }
        }
    });
});
