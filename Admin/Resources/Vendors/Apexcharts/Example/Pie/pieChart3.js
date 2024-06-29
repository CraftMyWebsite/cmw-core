var pieOptions3 = {
    chart: {
        type: 'pie'
    },
    series: [50, 30, 10, 10],
    labels: ['P1', 'P2', 'P3', 'P4']
};
var pieChart3 = new ApexCharts(document.querySelector("#pieChart3"), pieOptions3);
pieChart3.render();