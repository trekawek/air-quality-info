'use strict';

(function() {

const COLORS = [
    "#57b108",
    "#b0dd10",
    "#ffd911",
    "#e58100",
    "#990000"
];

function renderGraph(ctx, data, levels, levelNames) {
    var config = {
        type: 'bar',
        options: {
            scales: {
                xAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: __('Pollution level')
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: __('Days')
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        },
        data: {
            labels: levelNames,
            datasets: [{
                label: "",
                backgroundColor: COLORS,
                data: data
            }]
        }
    };
    if (typeof ctx.chart !== 'undefined') {
        ctx.chart.destroy();
        ctx.chart = null;
    } 
    ctx.chart = new Chart(ctx, config);
}

function updateGraph(graphContainer) {
    var dataset = graphContainer.dataset;
    var request = new XMLHttpRequest();
    request.open('GET', graphContainer.dataset.jsonUri, true);
    request.onload = function() {
        if (request.status == 200) {
            var data = JSON.parse(request.responseText);
            graphContainer.querySelectorAll('div.annual-graph-container').forEach(element => {
                var type = element.dataset.type;
                renderGraph(element.querySelector('canvas'), data[type]['days_by_levels'], data[type]['levels'], data['level_names']);
            });
        }
    };
    request.send();
}

document.querySelectorAll('div.annual-graphs').forEach(element => {
    updateGraph(element);
});

})();