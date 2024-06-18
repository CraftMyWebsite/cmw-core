var treemapOptions1 = {
    chart: {
        type: 'treemap'
    },
    series: [{
        data: [{
            x: 'A',
            y: 10
        }, {
            x: 'B',
            y: 20
        }, {
            x: 'C',
            y: 30
        }]
    }]
};
var treemapChart1 = new ApexCharts(document.querySelector("#treemapChart1"), treemapOptions1);
treemapChart1.render();