document.addEventListener('DOMContentLoaded', () => {
    const list_item = document.querySelectorAll('.list__item__component');


    list_item.forEach(e => e.addEventListener('click', () => {
        let menu = e.querySelector('.list__item__menu')
        menu.classList.toggle('close')
    }))
})