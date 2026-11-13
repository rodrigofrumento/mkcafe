import 'bootstrap';
import Inputmask from 'inputmask';

// Máscaras de formulário, ativadas por data-mask="..." nos inputs.
const masks = {
    phone: { mask: '(99) 9999-9999' },
    mobile: { mask: '(99) 99999-9999' },
    money: {
        alias: 'numeric',
        groupSeparator: '.',
        radixPoint: ',',
        digits: 2,
        digitsOptional: false,
        allowMinus: false,
        rightAlign: false,
    },
};

document.querySelectorAll('[data-mask]').forEach((input) => {
    const options = masks[input.dataset.mask];
    if (options) {
        Inputmask(options).mask(input);
    }
});
