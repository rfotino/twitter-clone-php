function updateHandleAvailability(inputId, outputId) {
    var input = document.getElementById(inputId);
    var handle = input.value;
    
    request = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    request.onreadystatechange = function() {
	if (request.readyState === 4 && request.status === 200) {
	    var output = document.getElementById(outputId);
	    output.innerHTML = request.responseText;
	}
    };
    request.open("GET", "api/handle-available.api.php?handle="+handle, true);
    request.send();
}