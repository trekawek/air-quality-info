'use strict';

window.chartColors = {
	red: 'rgb(255, 99, 132)',
	lightRed: 'rgba(255, 99, 132, 0.5)',
	orange: 'rgb(255, 159, 64)',
	lightOrange: 'rgba(255, 159, 64, 0.5)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	lightGreen: 'rgba(75, 192, 192, 0.5)',
	blue: 'rgb(54, 162, 235)',
	lightBlue: 'rgba(54, 162, 235, 0.5)',
	purple: 'rgb(153, 102, 255)',
	lightPurple: 'rgba(153, 102, 255, 0.5)',
    grey: 'rgb(169, 169, 169)',
    lightGrey: 'rgba(169, 169, 169, 0.5)',
    transparent: 'rgba(0, 0, 0, 0)'
};

(function() {

function mapToTimeSeries(data) {
    var result = new Array();
    var nullCounter, lastValidY;
    var y;
    for (var timeStamp in data) {
        if (data.hasOwnProperty(timeStamp)) {
            if (data[timeStamp] == null) {
                if (nullCounter++ < 10) {
                    y = lastValidY;
                } else {
                    y = null;
                }
            } else {
                y = data[timeStamp];
                nullCounter = 0;
                lastValidY = y;
            }
            result.push({
                t: new Date(timeStamp * 1000),
                y: y == null ? null : Math.round(y * 100) / 100
            });
        }
    }
    return result;
}

function emptyTimeSeries(data) {
    var ranges = new Array();
    var currentNullRange = null;
    for (var i in data) {
        if (data[i].y == null) {
            if (currentNullRange == null) {
                currentNullRange = {
                    from: {
                        t: data[i].t,
                        y: null
                    },
                    to: {
                        t: null,
                        y: null
                    },
                    steps: 0
                };
                if (i > 0) {
                    currentNullRange.from.y = data[i - 1].y;
                }
            } else {
                currentNullRange.steps++;
            }
        } else {
            if (currentNullRange != null) {
                currentNullRange.to.t = data[i - 1].t;
                currentNullRange.to.y = data[i].y;
                ranges.push(currentNullRange);
                currentNullRange = null;
            }
        }
    }
    
    var result = {};
    var from = null;
    var to = null;
    var step = null;
    for (var i in ranges) {
        var range = ranges[i];
        if (range.from.y == null || range.to.y == null || range.steps == 0) {
            continue;
        }

        var minTime = range.from.t.getTime();
        var maxTime = range.to.t.getTime();
        var stepT = (range.to.t.getTime() - range.from.t.getTime()) / range.steps;
        var stepY = (range.to.y - range.from.y) / range.steps;
        step = stepT;
        for (var j = 0; j <= range.steps; j++) {
            var mu = j / range.steps;
            var t = Math.round(interpolate(minTime, maxTime, mu));
            var y = Math.round(interpolate(range.from.y, range.to.y, mu) * 100) / 100;
            result[t] = y;
            if (from === null) {
                from = t;
            }
            to = t;
        }
    }

    var resultArray = [];
    if (from !== null) {
        for (var i = from; i < to; i += step) {
            if (result.hasOwnProperty(i)) {
                resultArray.push({t: new Date(i), y: result[i]});
            } else {
                resultArray.push({t: new Date(i), y: null});
            }
        }
    }
    return resultArray;
}

function interpolate(y1, y2, mu) {
    return (y1 * (1 - mu) + y2 * mu);
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
            spanGaps: false,
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
                        },
                        tooltipFormat: "MMM D HH:mm"
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
        var pm25data = mapToTimeSeries(data.data.pm25);
        var pm10data = mapToTimeSeries(data.data.pm10);
        var emptyPm25Data = emptyTimeSeries(pm25data);
        var emptyPm10Data = emptyTimeSeries(pm10data);

        config.data = {datasets: []};

        if (!isEmptyData(data.data.pm1)) {
            var pm1data = mapToTimeSeries(data.data.pm1);
            config.data.datasets.push({
                backgroundColor: window.chartColors.yellow,
                borderColor: window.chartColors.red,
                label: 'PM₁ (µg/m³)',
                data: pm1data,
                borderWidth: 1,
                hidden: true
            });
        }

        if (!isEmptyData(data.data.pm4)) {
            var pm4data = mapToTimeSeries(data.data.pm4);
            config.data.datasets.push({
                backgroundColor: window.chartColors.blue,
                borderColor: window.chartColors.red,
                label: 'PM₄ (µg/m³)',
                data: pm4data,
                borderWidth: 1,
                hidden: true
            });
        }

        config.data.datasets.push(
            {
                backgroundColor: window.chartColors.purple,
                borderColor: window.chartColors.red,
                label: 'PM₂.₅ (µg/m³)',
                data: pm25data,
                borderWidth: 1
            }, {
                backgroundColor: window.chartColors.orange,
                borderColor: window.chartColors.red,
                label: 'PM₁₀ (µg/m³)',
                data: pm10data,
                borderWidth: 1
            }, {
                backgroundColor: window.chartColors.lightPurple,
                borderColor: window.chartColors.lightRed,
                data: emptyPm25Data,
                borderWidth: 1
            }, {
                backgroundColor: window.chartColors.lightOrange,
                borderColor: window.chartColors.lightRed,
                data: emptyPm10Data,
                borderWidth: 1
            }
        );

        config.options.scales.yAxes = [{
            display: true,
            scaleLabel: {
                display: false
            },
            ticks: {
                suggestedMin: 0,
                suggestedMax: 55
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
                  content: __('PM₂.₅ limit'),
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

        case 'pm_n':
        config.data = {datasets: []};
        [{
            name: 'n05',
            label: '#PM₀.₅/cm³',
            hidden: true,
            borderColor: window.chartColors.grey
         },
         {
            name: 'n1',
            label: '#PM₁/cm³',
            hidden: true,
            borderColor: window.chartColors.yellow
         },
         {
            name: 'n25',
            label: '#PM₂.₅/cm³',
            borderColor: window.chartColors.purple
         },
         {
            name: 'n4',
            label: '#PM₄/cm³',
            hidden: true,
            borderColor: window.chartColors.blue
         },
         {
            name: 'n10',
            label: '#PM₁₀/cm³',
            borderColor: window.chartColors.orange
         }].forEach(function(item) {
             var d = mapToTimeSeries(data.data[item.name]);
             if (isEmptyData(d)) {
                 return;
             }
             var dataset = {
                data: mapToTimeSeries(data.data[item.name]),
                borderWidth: 2,
                fill: false
             };
             config.data.datasets.push(Object.assign(dataset, item));
         });
        break;

        case 'co2':
        var co2Data = mapToTimeSeries(data.data.co2);
        var emptyCo2Data = emptyTimeSeries(co2Data);
        config.data = {
            datasets: [{
                borderColor: window.chartColors.grey,
                label: __('CO₂') + ' (ppm)',
                data: co2Data,
                borderWidth: 2,
                fill: false
            },{
                borderColor: window.chartColors.lightGrey,
                data: emptyCo2Data,
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

        case 'temperature':
        var tempData = mapToTimeSeries(data.data.temperature);
        var detectorTempData = mapToTimeSeries(data.data.heater_temperature);

        var emptyTempData = emptyTimeSeries(tempData);

        config.data = {
            datasets: [{
                borderColor: window.chartColors.red,
                label: __('Temperature') + ' (°C)',
                data: tempData,
                borderWidth: 2,
                fill: false
            }, {
                borderColor: window.chartColors.lightRed,
                data: emptyTempData,
                borderWidth: 2,
                fill: false
            }
        ]};
        if (!isEmptyData(data.data.heater_temperature)) {
            config.data.datasets.push({
                borderColor: window.chartColors.lightRed,
                label: __('Detector temperature') + '(°C)',
                data: detectorTempData,
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
        var pressureData = mapToTimeSeries(data.data.pressure);
        var emptyPressureData = emptyTimeSeries(pressureData);
        config.data = {
            datasets: [{
                borderColor: window.chartColors.green,
                label: __('Pressure') + ' (hPa)',
                data: pressureData,
                borderWidth: 2,
                fill: false
            },{
                borderColor: window.chartColors.lightGreen,
                data: emptyPressureData,
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
        var humidityData = mapToTimeSeries(data.data.humidity);
        var detectorHumidityData = mapToTimeSeries(data.data.heater_humidity);
        var emptyHumidityData = emptyTimeSeries(humidityData);
        config.data = {
            datasets: [{
                borderColor: window.chartColors.blue,
                label: __('Humidity') + ' (%)',
                data: humidityData,
                borderWidth: 2,
                fill: false
            },{
                borderColor: window.chartColors.lightBlue,
                data: emptyHumidityData,
                borderWidth: 2,
                fill: false
            }]};
        if (!isEmptyData(data.data.heater_humidity)) {
            config.data.datasets.push({
                borderColor: window.chartColors.lightBlue,
                label: __('Detector humidity') + ' (%)',
                data: detectorHumidityData,
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

    config.options.legend = {
        labels: {
            filter: function(item, chart) {
                return typeof item.text != 'undefined';
            }
        }
    };

    ctx.chart = new Chart(ctx, config);
}

function updateGraph(graphContainer) {
    var dataset = graphContainer.dataset;
    var type = dataset.type;
    var range = dataset.range;
    var avgType = dataset.avgType;
    var ctx = graphContainer.querySelector('canvas.graph');

    var url = dataset.graphUri + '?type=' + type + '&range=' + range;
    if (typeof dataset.avgType !== 'undefined' && dataset.avgType != 0) {
        url += '&ma_h=' + dataset.avgType;
    }
    var request = new XMLHttpRequest();
    request.open('GET', url, true);
    request.onload = function() {
        if (request.status == 200) {
            var data = JSON.parse(request.responseText);
            renderGraph(ctx, data, type, avgType);
        }
    };
    request.send();
}

window.updateGraph = updateGraph;

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

document.querySelectorAll('.graph-avg-type button').forEach(element => {
    element.onclick = ev => {
        var avgType = element.dataset.avgType;
        selectAvgType(avgType);        
        document.querySelectorAll('.graph-container').forEach(graphContainer => {
            updateGraph(graphContainer);
        });
    };
});

})();