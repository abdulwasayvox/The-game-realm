function ueSwipeJs(){

  this.init = function(widgetSelector){

    const slider = document.querySelector(widgetSelector);

    let isDown = false;
    let startX;
    let scrollLeft;
    let classActive = "active";

    slider.addEventListener('mousedown', (e) => {
      isDown = true;
      slider.classList.add(classActive);
      startX = e.pageX - slider.offsetLeft;
      scrollLeft = slider.scrollLeft;
    });
  
    slider.addEventListener('mouseleave', () => {
      isDown = false;
      slider.classList.remove(classActive);
    });
  
    slider.addEventListener('mouseup', () => {
      isDown = false;
      slider.classList.remove(classActive);
    });
  
    slider.addEventListener('mousemove', (e) => {
      if(!isDown) return;
      e.preventDefault();
      const x = e.pageX - slider.offsetLeft;
      const walk = (x - startX) * 3; //scroll-fast
      slider.scrollLeft = scrollLeft - walk;
    });

  }
}