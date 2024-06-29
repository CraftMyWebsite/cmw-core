var radialBarOptions2 = {
    chart: {
        type: 'radialBar'
    },
    series: [44, 55, 67, 83],
    labels: ['V1', 'V2', 'V3', 'V4']
};
var radialBarChart2 = new ApexCharts(document.querySelector("#radialBarChart2"), radialBarOptions2);
radialBarChart2.render();