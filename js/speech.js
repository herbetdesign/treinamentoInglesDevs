async function validatePronunciation(correctPhrase, threshold) {
    return new Promise((resolve, reject) => {
        const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'en-US';
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        recognition.start();

        recognition.onresult = (event) => {
            const userSpeech = event.results[0][0].transcript.toLowerCase().trim();
            const expected = correctPhrase.toLowerCase().trim();
            
            // Calcula a porcentagem de acerto
            const userWords = userSpeech.split(' ');
            const expectedWords = expected.split(' ');
            const total = expectedWords.length;
            let matches = 0;

            expectedWords.forEach(word => {
                if (userWords.includes(word)) matches++;
            });

            const accuracy = (matches / total) * 100;
            resolve(accuracy >= threshold);
        };

        recognition.onerror = () => reject("Erro no microfone. Verifique as permissões.");
    });
}
