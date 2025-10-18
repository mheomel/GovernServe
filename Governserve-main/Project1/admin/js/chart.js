const ctx = document.getElementById('reportChart').getContext('2d');

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Pending', 'Completed', 'Rejected'],
    datasets: [{
      label: 'Reports',
      data: [105, 259, 45],
      backgroundColor: ['#f3c623', '#3cb371', '#ff6b6b'],
    }]
  },
  options: {
    responsive: false,  // prevents resizing loop
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});
