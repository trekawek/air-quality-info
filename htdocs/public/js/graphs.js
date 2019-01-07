'use strict';

window.chartColors = {
	red: 'rgb(255, 99, 132)',
	lightRed: 'rgb(255, 160, 181)',
	orange: 'rgb(255, 159, 64)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	blue: 'rgb(54, 162, 235)',
	lightBlue: 'rgb(120, 187, 232)',
	purple: 'rgb(153, 102, 255)',
	grey: 'rgb(201, 203, 207)'
};
moment.locale('pl');

const CONFIG = document.querySelector('body').dataset;

function renderGraph(ctx, data, type) {
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

    var config = {
        type: 'line',
        options: {
            responsive: true,
            aspectRatio: 2,
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
                label: 'PM₂₅ (µg/m³)',
                data: mapToTimeSeries(data.data.PM25),
                borderWidth: 1
            }, {
                backgroundColor: window.chartColors.orange,
                borderColor: window.chartColors.red,
                label: 'PM₁₀ (µg/m³)',
                data: mapToTimeSeries(data.data.PM10),
                borderWidth: 1
            }]
        };
        config.options.scales.yAxes = [{
            display: true,
            scaleLabel: {
                display: false
            }
        }];
        config.options.annotation = {
            annotations: [{
                type: 'line',
                mode: 'horizontal',
                scaleID: 'y-axis-0',
                value: CONFIG.pm25Limit,
                borderColor: 'purple',
                borderWidth: 1,
                label: {
                  content: 'Norma PM₂₅',
                  enabled: true,
                  position: 'left',
		          backgroundColor: 'rgba(0,0,0,0.3)'
                }
            },{
                type: 'line',
                mode: 'horizontal',
                scaleID: 'y-axis-0',
                value: CONFIG.pm10Limit,
                borderColor: 'orange',
                borderWidth: 1,
                label: {
                  content: 'Norma PM₁₀',
                  enabled: true,
                  position: 'left',
		          backgroundColor: 'rgba(0,0,0,0.3)'
                }
            }]
        };
    
        break;

        case 'temperature':
        config.data = {
            datasets: [{
                borderColor: window.chartColors.red,
                label: 'Temperatura (°C)',
                data: mapToTimeSeries(data.data.TEMPERATURE),
                borderWidth: 2,
                fill: false
            },{
                borderColor: window.chartColors.lightRed,
                label: 'Temperatura detektora (°C)',
                data: mapToTimeSeries(data.data.HEATER_TEMPERATURE),
                borderWidth: 2,
                fill: false,
                hidden: true
            }]
        };
        config.options.scales.yAxes = [{
            display: true,
            scaleLabel: {
                display: false
            }
        }];
        break;

        case 'pressure':
        config.data = {
            datasets: [{
                borderColor: window.chartColors.green,
                label: 'Ciśnienie (hPa)',
                data: mapToTimeSeries(data.data.PRESSURE),
                borderWidth: 2,
                fill: false
            }]
        };
        config.options.scales.yAxes = [{
            display: true,
            scaleLabel: {
                display: false
            }
        }];
        break;

        case 'humidity':
        config.data = {
            datasets: [{
                borderColor: window.chartColors.blue,
                label: 'Wilgotność (%)',
                data: mapToTimeSeries(data.data.HUMIDITY),
                borderWidth: 2,
                fill: false
            },{
                borderColor: window.chartColors.lightBlue,
                label: 'Wilgotność detektora (%)',
                data: mapToTimeSeries(data.data.HEATER_HUMIDITY),
                borderWidth: 2,
                fill: false,
                hidden: true
            }]
        };
        config.options.scales.yAxes = [{
            display: true,
            scaleLabel: {
                display: false
            }
        }];
        break;
    }

    if (typeof ctx.chart !== 'undefined') {
        ctx.chart.destroy();
        ctx.chart = null;
    } 
    ctx.chart = new Chart(ctx, config);
}

function updateGraph(graphContainer) {
    var dataset = graphContainer.dataset;
    var type = dataset.type;
    var range = dataset.range;
    var ctx = graphContainer.querySelector('canvas.graph');

    var request = new XMLHttpRequest();
    var url = '/graph_data.json?type=' + type + '&range=' + range;
    if (type == 'pm') {
        url += '&ma_h=1';
    }
    request.open('GET', url, true);
    request.onload = function() {
        if (request.status == 200) {
            var data = JSON.parse(request.responseText);
            renderGraph(ctx, data, type);
        }
    };
    request.send();
}

document.querySelectorAll('div.graph-container').forEach(element => {
    updateGraph(element);
});

document.querySelectorAll('.graph-range button').forEach(element => {
    element.onclick = ev => {
        var oldPrimary = document.querySelector('.graph-range button.btn-primary');
        oldPrimary.classList.remove('btn-primary');
        oldPrimary.classList.add('btn-secondary');

        element.classList.remove('btn-secondary');
        element.classList.add('btn-primary');
        
        var range = element.dataset.range;
        document.querySelectorAll('.graph-container').forEach(graphContainer => {
            graphContainer.dataset.range = range;
            updateGraph(graphContainer);
        });
    };
});
