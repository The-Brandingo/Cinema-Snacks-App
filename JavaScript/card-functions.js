function displayCard(displayPayID, displayName, displayCardNumber, displayExpiry, cardInfoBox, cvv) {

    // Display card information in the cardInfoBox
    cardInfoBox.innerHTML = `
    <div class="card-display">
        <p style="margin-right: 0; margin-bottom: 0; padding-right: 0;">${displayName}</p>
        <p style="padding: 1px; padding-left: 0; padding-right: 1px; margin: 1px; font-family: monospace;">
            ${strRepeat('•', 4)} ${strRepeat('•', 4)} ${strRepeat('•', 4)} ${strRepeat('•', 4)} ${displayCardNumber}
        </p>
        <form action="../PHP_Handling/card_handling.php" method="post" id="delete-card-form" style="display: flex; margin: 0; padding: 0;">
            <input type="hidden" name="payment_id" value="${displayPayID}">
            <p style="padding-right: 20px; margin: 1px;">Exp. ${displayExpiry}</p>
            ${cvv !== false ? `<input type="text" name="cvv" id = "cvv" maxlength="3" class="cvv" placeholder="CVV" style="margin: 1px;">` : ''}
            <input type="hidden" name="delete_card">
            <button name="delete_card" onclick="popoutToggler('delete-card-popout')" type="button" style='text-decoration: underline; font-weight: bold; font-size: 11px; font-family: monospace; color: black; padding: 0; margin: 1px; margin-bottom: 2px; margin-left: auto; border: 0; background: none;'>
                delete
            </button>
        </form>
    </div>
    `;
}


function deleteCard() {

    document.getElementById('delete-card-form').submit();
    popoutToggler('delete-card-popout');

}

document.getElementById('card-number').addEventListener('input', function (e) {
    var target = e.target, position = target.selectionEnd, length = target.value.length;

    // Remove any non-digit characters
    var value = target.value.replace(/\D/g, '');

    // Add a space every 4 digits
    var formattedValue = value.match(/.{1,4}/g)?.join(' ') ?? '';

    // Update the value of the input field
    target.value = formattedValue;

    // Restore the position of the cursor
    if (position === length && length > 0) {
        position = formattedValue.length;
    }
    target.setSelectionRange(position, position);
});
