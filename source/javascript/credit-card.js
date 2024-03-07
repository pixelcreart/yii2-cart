const cleaveZen = window.cleaveZen
const {
    formatCreditCard,
    getCreditCardType,
    registerCursorTracker,
    DefaultCreditCardDelimiter,
    unformatCreditCard,
    formatDate,
    formatGeneral,
  } = cleaveZen

const main = () => {
    const creditCardInput = document.querySelector('#checkoutform-ccnumber')
    const creditCardExpDate = document.querySelector('#checkoutform-ccexpdate')
    const creditCardCvv = document.querySelector('#checkoutform-cccvv')
    const creditCardIdentityId = document.querySelector('#checkoutform-ccholderidentity')

    let blocksValue = 3

    registerCursorTracker({
        input: creditCardInput,
        delimiter: DefaultCreditCardDelimiter,
    })

    creditCardInput.addEventListener('input', e => {
        const value = e.target.value
        creditCardInput.value = formatCreditCard(value)
        const creditCardType = getCreditCardType(value)

        if(creditCardType==='amex') {
            blocksValue = 4
        } else {
            blocksValue = 3
        }
    })

    creditCardCvv.addEventListener('input', e => {
        creditCardCvv.value = formatGeneral(e.target.value, {
            blocks: [blocksValue],
        })
    })

    creditCardIdentityId.addEventListener('input', e => {
        creditCardIdentityId.value = formatGeneral(e.target.value, {
            blocks: [14],
        })
    })

    creditCardExpDate.addEventListener('input', (e) => {
        creditCardExpDate.value = formatDate(e.target.value, {
            datePattern: ['m', 'y'],
        })
    })
}

main()