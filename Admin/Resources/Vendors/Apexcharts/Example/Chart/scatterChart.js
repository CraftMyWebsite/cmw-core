var scatterOptions = {
    chart: {
        type: 'scatter',
        zoom: {
            enabled: true,
            type: 'xy'
        }
    },
    series: [{
        name: "Série 1",
        data: [
            [16.4, 5.4],
            [21.7, 2],
            [25.4, 3],
            [19, 2],
            [10.9, 1]
        ]
    }, {
        name: "Série 2",
        data: [
            [36.4, 13.4],
            [1.7, 11],
            [5.4, 8],
            [9, 17],
            [1.9, 4]
        ]
    }]
};

var scatterChart = new ApexCharts(document.querySelector("#scatterChart"), scatterOptions);
scatterChart.render();