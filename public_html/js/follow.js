function followUser(userId, link) {
    request = new XMLHttpRequest();
    request.open("GET", "api/follow.api.php?id="+userId, false);
    request.send();
    if (request.responseText !== "") {
        link.innerHTML = request.responseText;
    }
}