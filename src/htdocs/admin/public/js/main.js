document.querySelectorAll('.delete-link').forEach(link => {
	link.onclick = (e => {
		var request = new XMLHttpRequest();
    	request.open('DELETE', link.href, true);
        request.onload = function () {
            location.reload();
        }
		request.send(null);
		return false;
	})
});