const sidebar = document.querySelector('#sidebar__sms')


let rotate = +sidebar.querySelector('.bi-chevron-down').style.rotate
sidebar.addEventListener('click', () => {
    rotate += 180
    sidebar.querySelector('.bi-chevron-down').style.rotate = rotate + "deg";
})