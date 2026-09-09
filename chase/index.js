let tokenInput = document.getElementById('tokenInput');
let checkBox = document.getElementById('checkBox');
let hold = document.getElementById('hold');
let contained = document.getElementById('contained');
let holder = document.querySelector('.holder')
let display = false;



function token() {

    if (display == false) {
        tokenInput.style.display = 'block';
      hold.style.height='60vh'
      contained.style.height='90vh'
      console.log('great');
      display = true;
    }
    else {
        tokenInput.style.display = 'none';
        hold.style.height='52vh'
         contained.style.height='80vh'
        console.log('greatest');
        display = false;
    }


    // if (checkBox.checked==true) {
    //     tokenInput.style.display='block';
    // }
    // else{
    //     tokenInput.style.display='none';
    // }


    // tokenInput.classList.toggle('divtoken')
}