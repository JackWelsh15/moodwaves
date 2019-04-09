$(document).ready(function(e){

var signInButton = document.getElementById('sign-in');
var signInModal = document.getElementById('sign-in-modal');
  signInButton.onclick = function(){
  signInModal.style.display = 'block';

}
(function() {
    var burger = document.querySelector('.burger');
    var menu = document.querySelector('#'+burger.dataset.target);
    burger.addEventListener('click', function() {
        burger.classList.toggle('is-active');
        menu.classList.toggle('is-active');
    });
})();
})
