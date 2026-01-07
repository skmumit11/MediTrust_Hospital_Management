function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");

    // toggle active class
    sidebar.classList.toggle("active");
}

// Auto-close sidebar on mobile/when clicking a link
document.addEventListener("DOMContentLoaded", function() {
    const links = document.querySelectorAll(".sidebar-menu a");
    links.forEach(link => {
        link.addEventListener("click", () => {
            document.getElementById("sidebar").classList.remove("active");
        });
    });
});
