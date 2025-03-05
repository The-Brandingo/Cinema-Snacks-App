function popoutToggler(popout_id) {
    var popout = document.getElementById(popout_id);
    popout.style.display = (popout.style.display === 'flex') ? 'none' : 'flex';
}

function hamburgerToggle() {
    var menu = document.getElementById('overlayMenu');
    menu.classList.toggle('active');
}