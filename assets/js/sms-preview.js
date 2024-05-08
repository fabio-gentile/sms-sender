const inputContent = document.querySelector('#sms_content')
const btnPreviewModal = document.querySelector('#btnPreviewModal')
const modalBodyPreview = document.querySelector('.modal-body.preview')

btnPreviewModal.addEventListener('click', () => {
    modalBodyPreview.textContent =  inputContent.value
})

