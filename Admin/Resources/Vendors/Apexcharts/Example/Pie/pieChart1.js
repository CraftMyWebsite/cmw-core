var pieOptions1 = {
    chart: {
        type: 'donut'
    },
    series: [44, 55, 41, 17],
    labels: ['A', 'B', 'C', 'D']
};
var pieChart1 = new ApexCharts(document.querySelector("#pieChart1"), pieOptions1);
pieChart1.render();