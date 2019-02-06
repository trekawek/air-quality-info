'use strict';

const CONFIG = document.querySelector('body').dataset;
const LOCALE = JSON.parse(CONFIG.locale);

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

function bindAvgTypeSwitch(element) {
    element.querySelectorAll('.switch-avg-type').forEach(link => {
        link.onclick = ev => {
            element.dataset.avgType = link.dataset.avgType;
            loadSensor(element);
        };
    });
}

function loadSensor(element) {
    element.innerHTML = document.querySelector('#spinner').innerHTML;

    var url = element.dataset.sensorsUri + '?avg_type=' + element.dataset.avgType;
    var request = new XMLHttpRequest();
    request.open('GET', url, true);
    request.onload = function() {
        if (request.status == 200) {
            element.innerHTML = request.responseText;
            element.querySelectorAll('.graph-container').forEach(graphContainer => {
                updateGraph(graphContainer);
            });
            bindAvgTypeSwitch(element);
        }
    };
    request.send();
}

function loadSensors() {
    document.querySelectorAll('.sensors').forEach(element => {
        loadSensor(element);
    });
}

document.querySelectorAll('.sensors').forEach(element => {
    bindAvgTypeSwitch(element);
});

document.querySelectorAll('.reload-sensors').forEach(element => {
    setInterval(function() { loadSensor(element); }, 3 * 60 * 1000);
});

document.querySelectorAll('.load-sensors').forEach(element => {
    loadSensor(element);
});