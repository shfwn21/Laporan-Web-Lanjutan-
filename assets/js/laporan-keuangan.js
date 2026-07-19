// =====================================================
// LAPORAN-KEUANGAN.JS — Chart Pemasukan vs Pengeluaran
// Requires: chartLabels, chartPemasukan, chartPengeluaran,
//           namaBulanFilter variables defined inline
// =====================================================

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartLaporan').getContext('2d');

    const gradientIncome = ctx.createLinearGradient(0, 0, 0, 350);
    gradientIncome.addColorStop(0, 'rgba(34, 197, 94, 0.2)');
    gradientIncome.addColorStop(1, 'rgba(34, 197, 94, 0.01)');

    const gradientExpense = ctx.createLinearGradient(0, 0, 0, 350);
    gradientExpense.addColorStop(0, 'rgba(239, 68, 68, 0.2)');
    gradientExpense.addColorStop(1, 'rgba(239, 68, 68, 0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: chartPemasukan,
                    borderColor: '#22c55e',
                    backgroundColor: gradientIncome,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#22c55e',
                },
                {
                    label: 'Pengeluaran',
                    data: chartPengeluaran,
                    borderColor: '#ef4444',
                    backgroundColor: gradientExpense,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ef4444',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: '#94a3b8',
                        font: { family: 'Inter', size: 12 },
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: '#1c1f2e',
                    titleColor: '#f1f5f9',
                    bodyColor: '#94a3b8',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        title: function(items) {
                            return 'Tanggal ' + items[0].label + ' ' + namaBulanFilter;
                        },
                        label: function(ctx) {
                            return ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 10, family: 'Inter' },
                        maxRotation: 0
                    }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: {
                        color: '#9ca3af',
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
