function favoritePost(postId, link) {
    request = new XMLHttpRequest();
    request.open("GET", "api/favorite.api.php?id="+postId, false);
    request.send();
    if (request.responseText !== "") {
        response = JSON.parse(request.responseText);
        if (response['success']) {
            if (response['favorite']) {
                link.className = "selected";
                link.innerHTML = "Unfavorite";
            } else {
                link.className = "";
                link.innerHTML = "Favorite";
            }
        } else if (response['error']) {
            alert(response['error']);
        }
    }
}