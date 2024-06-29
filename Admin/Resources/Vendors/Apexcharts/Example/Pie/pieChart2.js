var pieOptions2 = {
    chart: {
        type: 'pie'
    },
    theme: {
        monochrome: {
            enabled: true
        }
    },
    series: [20, 25, 30, 25],
    labels: ['X', 'Y', 'Z', 'W']
};
var pieChart2 = new ApexCharts(document.querySelector("#pieChart2"), pieOptions2);
pieChart2.render();