const cardPayment = document.querySelector('#cardPayment'),
    paypalPayment = document.querySelector('#paypalPayment'),
    cardPaymentContainer = document.querySelector('.cardPaymentContainer'),
    cardPaymentContainerInputs = document.querySelectorAll('.cardPaymentContainer input'),
    paypalPaymentContainer = document.querySelector('.paypalPaymentContainer');
    paypalPaymentContainerInput = document.querySelector('.paypalPaymentContainer input');

paypalPaymentContainer.style.display = 'none'

cardPayment.addEventListener('change', () => {
    if (cardPayment.checked) {
        cardPaymentContainer.style.display = 'block'
        paypalPaymentContainer.style.display = 'none'
        paypalPaymentContainerInput.required = false
        cardPaymentContainerInputs.forEach(each=> each.required = true)
        paypalPaymentContainerInput.value = ''
    }
})
paypalPayment.addEventListener('change', () => {
    if (paypalPayment.checked) {
        cardPaymentContainer.style.display = 'none'
        paypalPaymentContainer.style.display = 'block'
        paypalPaymentContainerInput.required = true
        cardPaymentContainerInputs.forEach(each=> {
            each.value = ''
            each.required = false
        })
    }
})

