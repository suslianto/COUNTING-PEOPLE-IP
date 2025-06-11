$(window).scroll(function() {
  if ($(this).scrollTop() > 50) {
    $(".btn__top").fadeIn();
  } else {
    $(".btn__top").fadeOut();
  }
})

$(".btn__top").click(function() {
  $("html, body").animate({
    scrollTop: 0
  }, 500);
  return false;
})