(function () {
    addEventListener('scroll', () => {
        if (scrollY > 100) {
            document.querySelector(".home-head-bg").style.background = '#212529';
        } else {
            document.querySelector(".home-head-bg").style.background = 'rgba(0, 0, 0, 0.575)';
        }
    });
})();