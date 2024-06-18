var heatmapOptions1 = {
    chart: {
        type: 'heatmap'
    },
    series: [{
        name: 'Metric1',
        data: generateData(12, {min: 0, max: 90})
    }]
};
var heatmapChart1 = new ApexCharts(document.querySelector("#heatmapChart1"), heatmapOptions1);
heatmapChart1.render();

function generateData(count, yrange) {
    var i = 0;
    var series = [];
    while (i < count) {
        var x = (i+1).toString();
        var y = Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min;

        series.push({
            x: x,
            y: y
        });
        i++;
    }
    return series;
}