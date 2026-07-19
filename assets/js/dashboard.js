// =====================================================
// DASHBOARD.JS — Chart Pemasukan 7 Hari Terakhir
// Requires: chartLabels & chartData variables defined inline
// =====================================================

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartPemasukan').getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(6, 182, 212, 0.3)');
    gradient.addColorStop(1, 'rgba(6, 182, 212, 0.01)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Pemasukan (Rp)',
                data: chartData,
                backgroundColor: gradient,
                borderColor: '#06b6d4',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.6,
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
                    ticks: { color: '#9ca3af', font: { size: 11, family: 'Inter' } }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11, family: 'Inter' },
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000).toFixed(1) + ' Jt';
                            if (value >= 1000) return (value / 1000).toFixed(0) + ' Rb';
                            return value;
                        }
                    }
                }
            }
        }
    });
});
