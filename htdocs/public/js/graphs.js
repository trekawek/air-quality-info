'use strict';

window.chartColors = {
	red: 'rgb(255, 99, 132)',
	orange: 'rgb(255, 159, 64)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	blue: 'rgb(54, 162, 235)',
	purple: 'rgb(153, 102, 255)',
	grey: 'rgb(201, 203, 207)'
};
moment.locale('pl');

function mapToTimeSeries(data) {
    var result = new Array();
    for(var timeStamp in data) {
        if (data.hasOwnProperty(timeStamp)) {
            result.push({
                t: new Date(timeStamp * 1000),
                y: data[timeStamp] == null ? null : Math.round(data[timeStamp] * 100) / 100
            });
        }
    }
    return result;
}

function renderGraph(ctx, data, type) {
    var config = {
        type: 'line',
        options: {
            responsive: true,
            aspectRatio: 3,
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                xAxes: [{
                    display: true,
                    type: 'time'
                }]
            },
            elements: {
                point: {
                    radius: 0
                }
            },
            time: {
                min: new Date(data.start * 1000),
                max: new Date(data.end * 1000)
            }
        }
    };
    switch (type) {
        case 'pm':
        config.data = {
            datasets: [{
                backgroundColor: window.chartColors.purple,
                borderColor: window.chartColors.red,
                label: 'PM₂₅',
                data: mapToTimeSeries(data.data.PM25),
                borderWidth: 1
            }, {
                backgroundColor: window.chartColors.orange,
                borderColor: window.chartColors.red,
                label: 'PM₁₀',
                data: mapToTimeSeries(data.data.PM10),
                borderWidth: 1
            }]
        };
        config.options.scales.yAxes = [{
            display: true,
            scaleLabel: {
                display: true,
                labelString: "µg/m³"
            }
        }];
        break;

        case 'temperature':
        config.data = {
            datasets: [{
                borderColor: window.chartColors.red,
                label: 'Temperatura',
                data: mapToTimeSeries(data.data.TEMPERATURE),
                borderWidth: 2,
                fill: false
            }]
        };
        config.options.scales.yAxes = [{
            display: true,
            scaleLabel: {
                display: true,
                labelString: "°C"
            }
        }];
        break;

        case 'pressure':
        config.data = {
            datasets: [{
                borderColor: window.chartColors.red,
                label: 'Ciśnienie',
                data: mapToTimeSeries(data.data.PRESSURE),
                borderWidth: 2,
                fill: false
            }]
        };
        config.options.scales.yAxes = [{
            display: true,
            scaleLabel: {
                display: true,
                labelString: "hPa"
            }
        }];
        break;

        case 'humidity':
        config.data = {
            datasets: [{
                borderColor: window.chartColors.red,
                label: 'Wilgotność',
                data: mapToTimeSeries(data.data.HUMIDITY),
                borderWidth: 2,
                fill: false
            }]
        };
        config.options.scales.yAxes = [{
            display: true,
            scaleLabel: {
                display: true,
                labelString: "%"
            }
        }];
        break;
    }

    if (window.chart) {
        window.chart.destroy();
    } 
    window.chart = new Chart(ctx, config);
}

function updateGraph() {
    var type = document.querySelector('.graph-type button.btn-primary').dataset.type;
    var range = document.querySelector('.graph-range button.btn-primary').dataset.range;
    var ctx = document.querySelector('canvas.graph');

    var request = new XMLHttpRequest();
    request.open('GET', '/graph_data.json?type=' + type + '&range=' + range, true);
    request.onload = function() {
        if (request.status == 200) {
            var data = JSON.parse(request.responseText);
            renderGraph(ctx, data, type);
        }
    };
    request.send();
}

document.querySelectorAll('.graph-type button').forEach(element => {
    element.onclick = ev => {
        var oldPrimary = document.querySelector('.graph-type button.btn-primary');
        oldPrimary.classList.remove('btn-primary');
        oldPrimary.classList.add('btn-secondary');

        element.classList.remove('btn-secondary');
        element.classList.add('btn-primary');
        updateGraph();
    };
});

document.querySelectorAll('.graph-range button').forEach(element => {
    element.onclick = ev => {
        var oldPrimary = document.querySelector('.graph-range button.btn-primary');
        oldPrimary.classList.remove('btn-primary');
        oldPrimary.classList.add('btn-secondary');

        element.classList.remove('btn-secondary');
        element.classList.add('btn-primary');
        updateGraph();
    };
});

updateGraph();