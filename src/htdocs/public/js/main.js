'use strict';

const CONFIG = document.querySelector('body').dataset;
const LOCALE = JSON.parse(CONFIG.locale);

moment.locale(CONFIG.currentLang);
moment.tz.setDefault(CONFIG.timezone);

/** @returns Cookie value as string or undefined if the cookie was not found. */
function getCookie(name) {
    var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return decodeURIComponent(match[2]);
}

function setCookie(name, value, maxAge = 60 * 60 * 24 * 365, path = '/') {
    document.cookie = `${name}=${encodeURIComponent(value)}; max-age=${maxAge}; path=${path}`;
}

/** @return The stored property value as string or undefined if the property was not found. */
function getProperty(key) {
    if (typeof(Storage) !== 'undefined') {
        const item = localStorage.getItem(key);
        return item !== null ? item : undefined;
    } else {
        return getCookie(key);
    }
}

function saveProperty(key, value) {
    if (typeof(Storage) !== 'undefined') {
        localStorage.setItem(key, value);
    } else {
        setCookie(key, value);
    }
}

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
    var url = element.dataset.sensorsUri;
    if (element.dataset.avgType) {
        url += '?avgType=' + encodeURIComponent(element.dataset.avgType);
    }
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