var treemapOptions2 = {
    chart: {
        type: 'treemap'
    },
    series: [{
        data: [{
            x: 'D',
            y: 40
        }, {
            x: 'E',
            y: 50
        }, {
            x: 'F',
            y: 60
        }]
    }]
};
var treemapChart2 = new ApexCharts(document.querySelector("#treemapChart2"), treemapOptions2);
treemapChart2.render();