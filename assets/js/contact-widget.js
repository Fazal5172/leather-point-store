document.addEventListener("DOMContentLoaded", function () {

    const widget = document.querySelector(".contact-widget");

    if (!widget) return;

    const toggle = widget.querySelector(".contact-toggle");

    // Open / Close

    toggle.addEventListener("click", function (e) {

        e.stopPropagation();

        widget.classList.toggle("active");

    });

    // Close when clicking outside

    document.addEventListener("click", function (e) {

        if (!widget.contains(e.target)) {

            widget.classList.remove("active");

        }

    });

    // Close using Escape key

    document.addEventListener("keydown", function (e) {

        if (e.key === "Escape") {

            widget.classList.remove("active");

        }

    });

});