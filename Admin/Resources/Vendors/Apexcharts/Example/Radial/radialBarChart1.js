var radialBarOptions1 = {
    chart: {
        type: 'radialBar'
    },
    series: [67],
    labels: ['Progress']
};
var radialBarChart1 = new ApexCharts(document.querySelector("#radialBarChart1"), radialBarOptions1);
radialBarChart1.render();