var barOptions = {
    chart: {
        type: 'bar'
    },
    series: [{
        name: 'Réalisations',
        data: [44, 55, 41, 64, 22, 43, 21, 49, 62]
    }],
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']
    }
};

var barChart = new ApexCharts(document.querySelector("#barChart"), barOptions);
barChart.render();