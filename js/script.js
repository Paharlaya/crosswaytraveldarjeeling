// Crossway Darjeeling Travel - Main JavaScript
document.addEventListener('DOMContentLoaded', function() {
  
  // Fade-up animation on scroll
  const fadeEls = document.querySelectorAll('.fade-up');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  }, { threshold: 0.15 });
  fadeEls.forEach(el => observer.observe(el));

  // AJAX demo for package cards
  document.querySelectorAll('.package-card .btn-primary-green, .package-card .btn-outline-blue').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const card = this.closest('.package-card');
      const title = card?.querySelector('.card-title')?.innerText || 'package';
      
      // Simulate AJAX request
      const xhr = new XMLHttpRequest();
      xhr.open('GET', 'package.php', true);
      xhr.onload = function() {
        if (xhr.status === 200) {
          // Success - show a notification
          alert(`✨ ${title} — we'll send you details shortly! (AJAX demo)`);
        }
      };
      xhr.onerror = function() {
        alert('Network error occurred. Please try again.');
      };
      xhr.send();
    });
  });

  // Smooth scroll for "Let's Travel" buttons
  document.querySelectorAll('.btn-primary-green[href*="contact"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const contactSection = document.querySelector('#contact');
      if (contactSection) {
        contactSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });

  console.log('Crossway Darjeeling Travel — website loaded successfully!');
});