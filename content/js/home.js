// Optional JavaScript to enhance functionality
document.addEventListener('DOMContentLoaded', () => {
    // Example: Smooth scroll effect
    const menuLinks = document.querySelectorAll('nav ul li a');

    menuLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            targetElement.scrollIntoView({ behavior: 'smooth' });
        });
    });
});