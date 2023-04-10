const ctx = document.getElementById('dashboardVisits');

const labels = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai'];
const data = {
    labels: labels,
    datasets: [
        {
            data: [
                0, 10, 5, 15, 8
            ],
            label: 'Visites',
            borderColor: '#df305c',
        },
        {
            label: 'Inscriptions',
            data: [
                0, 0, 2, 1, 4
            ],
            borderColor: '#5e4298',
        }
    ]
};

new Chart(ctx, {
    type: 'line',
    data: data,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
        }
    },
});