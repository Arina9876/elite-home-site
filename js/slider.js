// js/slider.js  –  показываем один слайд, листаем стрелками
document.addEventListener('DOMContentLoaded', function () {

    var slider = document.getElementById('hero-slider');
    if (!slider) return;
  
    var slides = slider.querySelectorAll('.slide');
    var total  = slides.length;
    var index  = 0;
  
    function show(i){
      slides[index].classList.remove('active');
      index = (i + total) % total;
      slides[index].classList.add('active');
    }
  
    // начальный слайд
    slides[index].classList.add('active');
  
    // кнопки
    slider.querySelector('.prev').addEventListener('click', function () {
      show(index - 1);
    });
    slider.querySelector('.next').addEventListener('click', function () {
      show(index + 1);
    });
  });