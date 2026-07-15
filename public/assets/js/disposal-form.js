(() => {
    'use strict';

    const form = document.querySelector('[data-disposal-form]');

    if (!form) {
        return;
    }

    const typeSelect = form.querySelector('[data-disposal-type]');
    const donationSection = form.querySelector('[data-donation-fields]');
    const requiredFields = form.querySelectorAll(
        '[data-donation-required]'
    );

    if (!typeSelect || !donationSection) {
        return;
    }

    const updateDonationFields = () => {
        const option = typeSelect.options[typeSelect.selectedIndex];
        const isDonation = option?.dataset.typeCode === 'DONACION';

        donationSection.hidden = !isDonation;

        requiredFields.forEach((field) => {
            field.required = isDonation;
        });
    };

    typeSelect.addEventListener('change', updateDonationFields);
    updateDonationFields();
})();
