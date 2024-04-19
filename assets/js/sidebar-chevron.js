const sidebars = document.querySelectorAll('.sidebar')

sidebars.forEach((sidebar) => {
    const links = sidebar.querySelectorAll('[data-bs-toggle="collapse"]')

    links.forEach((e) => {
        let rotate = +e.querySelector('.bi-chevron-down').style.rotate
        e.addEventListener('click', () => {
            rotate += 180
            e.querySelector('.bi-chevron-down').style.rotate = rotate + "deg";
        })
    })
})


