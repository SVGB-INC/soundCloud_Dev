for (let i = 0; i < 99; i++) {
  $(`.res${i}`).slick({
    dots: false,
    speed: 700,
    infinite: true,
    slidesToShow: 6,
    slidesToScroll: 6,
    prevArrow:
      '<span class="prevArrow sliderArrow"><i class="fas fa-angle-left"></i></span>',
    nextArrow:
      '<span class="nextArrow sliderArrow"><i class="fas fa-angle-right"></i></span>',
    responsive: [
      {
        breakpoint: 1399,
        settings: {
          slidesToShow: 5,
          slidesToScroll: 5
        }
      },
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 4,
          slidesToScroll: 4
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3
        }
      },
      {
        breakpoint: 575,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 475,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });
}
