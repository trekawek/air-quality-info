'use strict';

const CONFIG = document.querySelector('body').dataset;
const LOCALE = JSON.parse(CONFIG.locale);

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

moment.locale(CONFIG.currentLang);

function __(msg) {
    if (typeof LOCALE[msg] != 'undefined') {
        return LOCALE[msg];
    } else {
        if (CONFIG.currentLang != 'en') {
            console.log("Unknown msg: [" + msg + "] for locale [" + CONFIG.currentLang +"]");
        }
        return msg;
    }
}

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

function isEmptyData(data) {
    for(var timeStamp in data) {
        if (data.hasOwnProperty(timeStamp)) {
            if (data[timeStamp] !== null) {
                return false;
            }
        }
    }
    return true;
}


function renderGraph(ctx, data, type, avgType) {
    var config = {
        type: 'line',
        options: {
            spanGaps: true,
            responsive: true,
            aspectRatio: 2,
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                xAxes: [{
                    display: true,
                    type: 'time',
                    time: {
                        displayFormats: {
                            millisecond: 'HH:mm:ss.SSS',
                            second: 'HH:mm:ss',
                            minute: 'HH:mm',
                            hour: 'HH',
                        }
                    }    
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
                data: mapToTimeSeries(data.data.pm25),
                borderWidth: 1
            }, {
                backgroundColor: window.chartColors.orange,
                borderColor: window.chartColors.red,
                label: 'PM₁₀ (µg/m³)',
                data: mapToTimeSeries(data.data.pm10),
                borderWidth: 1
            }]
        };
        config.options.scales.yAxes = [{
            display: true,
            scaleLabel: {
                display: false
            }
        }];

        var pm25Limit = null;
        var pm10Limit = null;
        if (avgType == 1) {
            pm25Limit = CONFIG.pm25Limit1h;
            pm10Limit = CONFIG.pm10Limit1h;
        } else if (avgType == 24) {
            pm25Limit = CONFIG.pm25Limit24h;
            pm10Limit = CONFIG.pm10Limit24h;
        }
        config.options.annotation = { annotations: [] };
        
        if (pm25Limit) {
            config.options.annotation.annotations.push({
                type: 'line',
                mode: 'horizontal',
                scaleID: 'y-axis-0',
                value: pm25Limit,
                borderColor: 'purple',
                borderWidth: 1,
                label: {
                  content: __('PM₂₅ limit'),
                  enabled: true,
                  position: 'left',
		          backgroundColor: 'rgba(0,0,0,0.3)'
                }
            });
        }
        if (pm10Limit) {
            config.options.annotation.annotations.push({
                type: 'line',
                mode: 'horizontal',
                scaleID: 'y-axis-0',
                value: pm10Limit,
                borderColor: 'orange',
                borderWidth: 1,
                label: {
                  content: __('PM₁₀ limit'),
                  enabled: true,
                  position: 'left',
		          backgroundColor: 'rgba(0,0,0,0.3)'
                }
            });
        }
        break;

        case 'temperature':
        config.data = {
            datasets: [{
                borderColor: window.chartColors.red,
                label: __('Temperature') + ' (°C)',
                data: mapToTimeSeries(data.data.temperature),
                borderWidth: 2,
                fill: false
            }]};
        if (!isEmptyData(data.data.heater_temperature)) {
            config.data.datasets.push({
                borderColor: window.chartColors.lightRed,
                label: __('Detector temperature') + '(°C)',
                data: mapToTimeSeries(data.data.heater_temperature),
                borderWidth: 2,
                fill: false,
                hidden: true
            });
        }
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
                label: __('Pressure') + ' (hPa)',
                data: mapToTimeSeries(data.data.pressure),
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
                label: __('Humidity') + ' (%)',
                data: mapToTimeSeries(data.data.humidity),
                borderWidth: 2,
                fill: false
            }]};
        if (!isEmptyData(data.data.heater_humidity)) {
            config.data.datasets.push({
                borderColor: window.chartColors.lightBlue,
                label: __('Detector humidity') + ' (%)',
                data: mapToTimeSeries(data.data.heater_humidity),
                borderWidth: 2,
                fill: false,
                hidden: true
            });
        }
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
    var avgType = dataset.avgType;
    var ctx = graphContainer.querySelector('canvas.graph');

    var request = new XMLHttpRequest();
    var url = '/' + CONFIG.deviceName + '/graph_data.json?type=' + type + '&range=' + range;
    if (typeof dataset.avgType !== 'undefined' && dataset.avgType != 0) {
        url += '&ma_h=' + dataset.avgType;
    }
    request.open('GET', url, true);
    request.onload = function() {
        if (request.status == 200) {
            var data = JSON.parse(request.responseText);
            renderGraph(ctx, data, type, avgType);
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
        switch (range) {
            case 'day':
            selectAvgType(1);
            break;

            case 'week':
            case 'month':
            selectAvgType(24);
            break;

            case 'year':
            selectAvgType(720);
            break;
        }
        document.querySelectorAll('.graph-container').forEach(graphContainer => {
            graphContainer.dataset.range = range;
            updateGraph(graphContainer);
        });
    };
});

function selectAvgType(avgType) {
    var oldPrimary = document.querySelector('.graph-avg-type button.btn-primary');
    oldPrimary.classList.remove('btn-primary');
    oldPrimary.classList.add('btn-secondary');

    var element = document.querySelector('.graph-avg-type button[data-avg-type="' + avgType + '"]')
    element.classList.remove('btn-secondary');
    element.classList.add('btn-primary');

    document.querySelectorAll('.graph-container').forEach(graphContainer => {
        graphContainer.dataset.avgType = avgType;
    });
}

document.querySelectorAll('.graph-avg-type button').forEach(element => {
    element.onclick = ev => {
        var avgType = element.dataset.avgType;
        selectAvgType(avgType);        
        document.querySelectorAll('.graph-container').forEach(graphContainer => {
            updateGraph(graphContainer);
        });
    };
});
