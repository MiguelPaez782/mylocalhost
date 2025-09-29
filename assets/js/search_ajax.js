
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("search_project");
    const container = document.getElementById("projects-container");

    searchInput.addEventListener("keyup", function() {
        const query = this.value;

        const xhr = new XMLHttpRequest();
        xhr.open("GET", "get_projects.php?search=" + encodeURIComponent(query), true);

        xhr.onload = function() {
            if (this.status === 200) {
                container.innerHTML = this.responseText;
            }
        };

        xhr.send();
    });
});