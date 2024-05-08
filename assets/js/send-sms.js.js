import count from "./sms-counter.js";

const modalBodyPreview = document.querySelector('.modal-body.preview')
const inputContent = document.querySelector('#sms_content')
const btnPreviewModal = document.querySelector('#btnPreviewModal')
const btnSendSms = document.querySelector('#btnSubmitModal')


btnPreviewModal.addEventListener('click', () => {
    modalBodyPreview.textContent =  inputContent.value
})

inputContent.addEventListener('input', (e) => {
    btnPreviewModal.disabled = e.target.value.length < 1;
    btnSendSms.disabled = e.target.value.length < 1;
    updateSmsCounter(e.target.value)
})

const smsCounter = () => {
    inputContent.parentElement.classList.add('position-relative')
    inputContent.parentElement.classList.add('overflow-hidden')
    const div = document.createElement('div')
    div.className = 'position-absolute top-100 text-black-50 translate-middle'
    div.id = 'smsCounter'
    inputContent.parentNode.appendChild(div)
}

smsCounter()

const updateSmsCounter = (e) => {
    const counter = count(e)
    const smsCounter = document.querySelector('#smsCounter')
    // console.log('counter', counter)
    smsCounter.textContent = counter.length + '/160'

    if (+counter.length > +counter.perMessage) {
        smsCounter.classList.add('excess')
        btnSendSms.disabled = true
        btnPreviewModal.disabled = true
    } else if (+counter.length !== 0) {
        smsCounter.classList.remove('excess')
        btnSendSms.disabled = false
        btnPreviewModal.disabled = false
    }
}

updateSmsCounter('')

const submitCountdown = () => {
    let timer = 5
    let interval = null
    const btn = document.querySelector('#modalSubmit')
    const btnOriginalText = btn.textContent
    btn.disabled = true

    const oneSecondDelay = () => {
        interval = setInterval((decreaseTimer) , 1000)
    }

    const decreaseTimer = () => {
        btn.textContent = timer !== 0 ? `(${timer}) ${btnOriginalText}` : btnOriginalText

        if (timer > 0) {
            timer--
        } else {
            clearInterval(interval)
            btn.disabled = false
        }
    }

    const submitModal = document.querySelector('#submitModal')
    submitModal.addEventListener('hidden.bs.modal', event => {
        clearTimer()
    })

    const clearTimer = () => {
        clearInterval(interval)
        timer = 5
        interval = null
        btn.textContent = btnOriginalText
    }
    decreaseTimer()
    oneSecondDelay()
}

btnSendSms.addEventListener('click', submitCountdown)

document.querySelector('form').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        const textarea = document.querySelector('textarea')
        
        if (e.target !== textarea) {
            e.preventDefault()
        }
    }
})