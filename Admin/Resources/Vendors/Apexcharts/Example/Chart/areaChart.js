var areaOptions = {
    chart: {
        type: 'area'
    },
    series: [{
        name: 'Engagement',
        data: [31, 40, 28, 51, 42, 109, 100]
    }],
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']
    }
};

var areaChart = new ApexCharts(document.querySelector("#areaChart"), areaOptions);
areaChart.render();