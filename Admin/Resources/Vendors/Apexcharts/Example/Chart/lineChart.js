var lineOptions = {
    chart: {
        type: 'line'
    },
    series: [{
        name: 'Ventes',
        data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
    }],
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']
    }
};

var lineChart = new ApexCharts(document.querySelector("#lineChart"), lineOptions);
lineChart.render();