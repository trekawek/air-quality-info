'use strict';

const CONFIG = document.querySelector('body').dataset;
const LOCALE = JSON.parse(CONFIG.locale);

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

document.querySelectorAll('.delete-link').forEach(link => {
	link.onclick = (e => {
        if (!confirm(__('Are you sure to delete this resource?'))) {
            return false;
        }
		var request = new XMLHttpRequest();
    	request.open('DELETE', link.href, true);
        request.onload = function () {
            location.href = location.href;
        }
		request.send(null);
		return false;
	})
});