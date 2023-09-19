// Scroll to top button

const scrollToTopButton = document.getElementById('scroll-to-top-button');

window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    scrollToTopButton.style.display = "block";
  } else {
    scrollToTopButton.style.display = "none";
  }
}

function scrollToTop() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}

//GALLERY
const overlay = document.querySelector('.overlay');
		const imgOverlay = document.querySelector('.img-overlay');
		const imgLinks = document.querySelectorAll('.img-link');
		const closeBtn = document.querySelector('.close-btn');

		imgLinks.forEach(imgLink => {
			imgLink.addEventListener('click', e => {
				e.preventDefault();
				imgOverlay.src = imgLink.href;
				overlay.classList.add('active');
			});
		});

		closeBtn.addEventListener('click', () => {
			overlay.classList.remove('active');
		});