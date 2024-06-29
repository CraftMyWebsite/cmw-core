var radialBarOptions3 = {
    chart: {
        type: 'radialBar'
    },
    series: [76, 67, 61, 90],
    labels: ['A', 'B', 'C', 'D']
};
var radialBarChart3 = new ApexCharts(document.querySelector("#radialBarChart3"), radialBarOptions3);
radialBarChart3.render();