document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    let currentSlideIndex = 0;

    const updateCarousel = () => {
        slides.forEach((slide, index) => {
            slide.classList.toggle('active', index === currentSlideIndex);
        });
    };

    const goToNextSlide = () => {
        currentSlideIndex = (currentSlideIndex + 1) % slides.length;
        updateCarousel();
    };

    const goToPrevSlide = () => {
        currentSlideIndex = (currentSlideIndex - 1 + slides.length) % slides.length;
        updateCarousel();
    };

    if (!slides.length || !prevBtn || !nextBtn) {
        return;
    }

    nextBtn.addEventListener('click', goToNextSlide);
    prevBtn.addEventListener('click', goToPrevSlide);

    updateCarousel();
});
