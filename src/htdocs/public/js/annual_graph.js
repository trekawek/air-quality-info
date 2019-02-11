'use strict';

(function() {

const COLORS = [
    "#57b108",
    "#b0dd10",
    "#ffd911",
    "#e58100",
    "#990000"
];

function generateTooltips(name, levels) {
    var tooltips = [];
    for (var i = 0; i < levels.length; i++) {
        var tooltip = ' ' + name + ' ' 
        if (i == levels.length - 1) {
            tooltip += '> '
            tooltip += levels[i];
        } else {
            tooltip += levels[i];
            tooltip += '-';
            tooltip += levels[i + 1];
        }
        tooltip += 'µg/m³';
        tooltips.push(tooltip);
    }
    return tooltips;
}

function renderGraph(ctx, data) {
    var tooltips = [];
    tooltips.push(generateTooltips('PM₂₅', data['pm25']['levels']));
    tooltips.push(generateTooltips('PM₁₀', data['pm10']['levels']));

    var config = {
        type: 'bar',
        options: {
            tooltips: {
                callbacks: {
                    label: function(item) {
                        return tooltips[item.datasetIndex][item.index] + ": " + item.yLabel + ' ' + __('days');
                    }
                }
            },        
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
            labels: data['level_names'],
            datasets: [{
                backgroundColor: window.chartColors.purple,
                borderColor: window.chartColors.red,
                label: 'PM₂₅',
                data: data['pm25']['days_by_levels']
            },{
                backgroundColor: window.chartColors.orange,
                borderColor: window.chartColors.red,
                label: 'PM₁₀',
                data: data['pm10']['days_by_levels']
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
                renderGraph(element.querySelector('canvas'), data);
            });
        }
    };
    request.send();
}

document.querySelectorAll('div.annual-graphs').forEach(element => {
    updateGraph(element);
});

})();