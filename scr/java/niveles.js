document.addEventListener('DOMContentLoaded', () => {
    const feedback = document.getElementById('nivel-1');
    const choices = [...document.querySelectorAll('#multiple-choice button')];
    const solveButton = document.querySelector('[data-action="solve"]');
    const retryButton = document.querySelector('[data-action="retry"]');
    const nextLink = document.getElementById('proximonivel');

    if (!feedback || choices.length === 0) return;

    const setChoicesDisabled = (disabled) => {
        choices.forEach((choice) => {
            choice.disabled = disabled;
        });
    };

    choices.forEach((choice) => {
        choice.addEventListener('click', () => {
            const isCorrect = choice.dataset.correct === 'true';
            feedback.textContent = isCorrect
                ? 'Respuesta correcta.'
                : 'Respuesta incorrecta. Intenta de nuevo.';
            feedback.style.color = isCorrect ? 'green' : 'red';
            setChoicesDisabled(true);

            if (isCorrect) {
                if (nextLink) {
                    nextLink.hidden = false;
                }
                retryButton.hidden = true;
                if (typeof window.sumarXP === 'function') window.sumarXP(100);
                return;
            }

            retryButton.hidden = false;
        });
    });

    solveButton.addEventListener('click', () => {
        feedback.textContent = document.body.dataset.solution;
        feedback.style.color = '';
    });

    retryButton.addEventListener('click', () => {
        feedback.textContent = '';
        feedback.style.color = '';
        retryButton.hidden = true;
        setChoicesDisabled(false);
    });
});