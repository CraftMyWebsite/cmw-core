var radarOptions1 = {
    chart: {
        type: 'radar'
    },
    series: [{
        name: 'Series 1',
        data: [80, 50, 30, 40, 100, 20]
    }],
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
    }
};
var radarChart1 = new ApexCharts(document.querySelector("#radarChart1"), radarOptions1);
radarChart1.render();