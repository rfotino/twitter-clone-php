function deletePost(postId) {
    var confirmed = confirm("Are you sure you would like to delete this post? This action cannot be undone.");
    if (confirmed) {
        var request = new XMLHttpRequest();
        request.open("POST", "api/delete-post.api.php?id=" + postId, false);
        request.send();
        alert(request.responseText);
        if (JSON.parse(request.responseText)) {
            var post = document.getElementById("post-"+postId);
            post.className += " hidden";
            var postStyle = post.currentStyle || window.getComputedStyle(post);
            post.style.marginTop = "-"+post.offsetHeight+"px";
        }
    }
}