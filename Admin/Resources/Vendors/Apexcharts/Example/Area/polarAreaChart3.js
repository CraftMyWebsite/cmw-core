var polarAreaOptions3 = {
    chart: {
        type: 'polarArea'
    },
    theme: {
        monochrome: {
            enabled: true,
            shadeTo: 'light',
            shadeIntensity: 0.6
        }
    },
    series: [10, 15, 25, 30, 20],
    labels: ['Apple', 'Orange', 'Banana', 'Grape', 'Pineapple']
};
var polarAreaChart3 = new ApexCharts(document.querySelector("#polarAreaChart3"), polarAreaOptions3);
polarAreaChart3.render();