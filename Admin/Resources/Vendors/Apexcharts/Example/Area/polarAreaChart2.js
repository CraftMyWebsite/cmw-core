var polarAreaOptions2 = {
    chart: {
        type: 'polarArea'
    },
    series: [20, 30, 40, 25],
    labels: ['Jan', 'Feb', 'Mar', 'Apr']
};
var polarAreaChart2 = new ApexCharts(document.querySelector("#polarAreaChart2"), polarAreaOptions2);
polarAreaChart2.render();