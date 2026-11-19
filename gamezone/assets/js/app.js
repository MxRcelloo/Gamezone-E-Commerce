document.addEventListener('DOMContentLoaded', () => {
    const quantity = document.querySelector('#quantity');
    if (quantity) {
        quantity.addEventListener('change', () => {
            const min = Number(quantity.min || 1);
            const max = Number(quantity.max || 999);
            let value = Number(quantity.value || min);
            quantity.value = Math.min(max, Math.max(min, value));
        });
    }
});
