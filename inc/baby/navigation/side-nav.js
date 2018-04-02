
      // SELECTING ELEMENTS
      const navButton = document.querySelector('.menu-button');
      const sideNav = document.querySelector('.slide-out');
      const sideNavOverlay = document.createElement('div')
      const bodyEl = document.querySelector('body')
      const sideNavClose = document.querySelector('.sidenav-close')
      const accordion = document.querySelector('.accordion')
      let isActive = false;
      let openNav, closeNav



      // SETUP SIDENAV
      sideNavOverlay.setAttribute('class', 'slide-out-overlay')
      bodyEl.append(sideNavOverlay)
      sideNav.style.transform = "translateX(105%)"








      // ONCLICK EVENTS
      sideNavClose.onclick = () => {

        sideNavOverlay.style.opacity = '0'
        sideNavOverlay.style.display = 'none';

        clearInterval(openNav)
        closeNav = setInterval(() => {

          sideNav.style.transform = "translateX(105%)"
        }, 1)
      }




      sideNavOverlay.onclick = () => {

        sideNavOverlay.style.opacity = '0'
        sideNavOverlay.style.display = 'none';

        clearInterval(openNav)
        closeNav = setInterval(() => {

          sideNav.style.transform = "translateX(105%)"
        }, 1)
      }




      navButton.onclick = () => {


        clearInterval(closeNav)
        openNav = setInterval(() => {
          sideNav.style.transform = "translateX(0%)"
          sideNavOverlay.style.display = 'block';
          sideNavOverlay.style.opacity = '1'
        }, 1)

      }

      accordion.onclick = e => {
        let count = e.target.nextElementSibling.children[0].childElementCount



        console.log(e.target.nextElementSibling);
        console.log(count);
        console.log(`${50 * count}px`);


        if (! isActive) {
          e.target.nextElementSibling.style.height = `${50 * count}px`
          isActive = true
        } else {
          e.target.nextElementSibling.style.height = `0.001px`
          isActive = false
        }
      }
