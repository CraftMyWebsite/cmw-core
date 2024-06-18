var polarAreaOptions1 = {
    chart: {
        type: 'polarArea'
    },
    series: [14, 23, 21, 17, 15],
    labels: ['A', 'B', 'C', 'D', 'E']
};
var polarAreaChart1 = new ApexCharts(document.querySelector("#polarAreaChart1"), polarAreaOptions1);
polarAreaChart1.render();