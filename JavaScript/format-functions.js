function partialHash(cardNumber) {
    var lastFourDigits = cardNumber.slice(-4);
    var hashedPart = '*'.repeat(cardNumber.length - 4);
    return hashedPart + lastFourDigits;
}

function strRepeat(str, num) {
    return new Array(num + 1).join(str);
}

function formatExpiryDate(input) {
    let cleanedInput = input.value.replace(/\D/g, '');

    if (cleanedInput.length > 2) {
        cleanedInput = cleanedInput.substring(0, 2) + '/' + cleanedInput.substring(2);
    }

    input.value = cleanedInput;
}

function prepareAndSubmit() {

    var cardNumberInput = document.getElementById('card-number');
    var cardNumber = cardNumberInput.value;

    // Update the input field with the value without spaces
    cardNumberInput.value = cardNumber.replace(/\s+/g, '');

    // Now submit the form
    document.getElementById('add-card-form').submit();
}
