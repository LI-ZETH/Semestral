'use strict';

(() => {
    const dialog = document.querySelector('[data-gallery-dialog]');
    const dialogImage = dialog?.querySelector('[data-dialog-image]');
    const closeButton = dialog?.querySelector('[data-dialog-close]');

    document.querySelectorAll('[data-gallery-image]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!dialog || !dialogImage) {
                return;
            }

            dialogImage.src = button.dataset.imageSrc || '';
            dialogImage.alt = button.dataset.imageAlt || 'Imagen del activo';

            if (typeof dialog.showModal === 'function') {
                dialog.showModal();
            }
        });
    });

    closeButton?.addEventListener('click', () => {
        dialog?.close();
    });

    dialog?.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });

    document.querySelectorAll('[data-print-page]').forEach((button) => {
        button.addEventListener('click', () => {
            window.print();
        });
    });

    document.querySelectorAll('[data-copy-url]').forEach((button) => {
        button.addEventListener('click', async () => {
            const value = button.dataset.copyValue || '';
            const feedback = document.querySelector('[data-copy-feedback]');

            if (value === '') {
                return;
            }

            try {
                await navigator.clipboard.writeText(value);

                if (feedback) {
                    feedback.textContent = 'Enlace copiado.';
                }
            } catch (error) {
                const temporaryInput = document.createElement('textarea');
                temporaryInput.value = value;
                temporaryInput.setAttribute('readonly', 'readonly');
                temporaryInput.style.position = 'fixed';
                temporaryInput.style.opacity = '0';
                document.body.appendChild(temporaryInput);
                temporaryInput.select();
                document.execCommand('copy');
                temporaryInput.remove();

                if (feedback) {
                    feedback.textContent = 'Enlace copiado.';
                }
            }
        });
    });
})();
