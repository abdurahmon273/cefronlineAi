document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('input[data-mask="number"]');
    const customInputs = document.querySelectorAll('input[data-mask="number_or_email"]');

    inputs.forEach(input => {
        input.addEventListener('input', () => {
            uzbPhoneMask(input);
        });
    });

    customInputs.forEach(input => {
        input.addEventListener('input', () => {
            uzbPhoneOrEmailMask(input);
        });
    });
});

function uzbPhoneMask(input) {
    // faqat raqamlar
    let digits = input.value.replace(/\D/g, '');

    // maksimal 9 ta raqam (94 107 05 24)
    digits = digits.substring(0, 9);

    let result = '';

    if (digits.length > 0) {
        result = '(' + digits.substring(0, 2);
    }
    if (digits.length >= 3) {
        result += ') ' + digits.substring(2, 5);
    }
    if (digits.length >= 6) {
        result += '-' + digits.substring(5, 7);
    }
    if (digits.length >= 8) {
        result += '-' + digits.substring(7, 9);
    }

    input.value = result;
}
function uzbPhoneOrEmailMask(input) {
    let value = input.value;

    // 3 ta belgidan kam bo'lsa hech narsa qilmaydi
    if (value.length < 3) {
        console.log('ana : ',value)
        return;
    }

    // birinchi 3 ta belgi
    let firstThree = value.substring(0, 3);

    // agar birinchi 3 ta belgi faqat raqam bo'lsa phone mask
    if ( /^[\d()\-\s]+$/.test(value)) {
        console.log('faqat raqam',firstThree)
        let digits = value.replace(/\D/g, '');

        digits = digits.substring(0, 9);

        let result = '';

        if (digits.length > 0) {
            result = '(' + digits.substring(0, 2);
        }
        if (digits.length >= 3) {
            result += ') ' + digits.substring(2, 5);
        }
        if (digits.length >= 6) {
            result += '-' + digits.substring(5, 7);
        }
        if (digits.length >= 8) {
            result += '-' + digits.substring(7, 9);
        }

        input.value = result;
    } else {
        console.log('faqat raqam emas',firstThree)

        if (value.includes('@') && !value.includes('@gmail.com')) {
            let parts = value.split('@');

            // faqat 1 marta @ bo‘lsa ishlaydi
            if (parts.length === 2 && parts[1] === '') {
                input.value = parts[0] + '@gmail.com';

                // cursorni oxiriga olib borish
                setTimeout(() => {
                    input.setSelectionRange(input.value.length, input.value.length);
                }, 0);

                return;
            }
        }
    }
}
