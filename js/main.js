// Variáveis globais
let currentPhrase = 0;
let phrases = [];
let currentAccuracy = 100; // Padrão: 100%

// Carrega todas as frases do backend
async function loadPhrases() {
    try {
        const response = await fetch('php/get_questions.php');
        if (!response.ok) throw new Error("Erro ao carregar frases!");
        phrases = await response.json();
        console.log("Frases carregadas:", phrases); // Verifique no console do navegador
        showPhrase();
    } catch (error) {
        console.error(error);
        alert("Erro ao carregar dados. Verifique o console.");
    }
}

// Exibe a frase atual
function showPhrase() {
    const phrase = phrases[currentPhrase];
    document.getElementById('phrase').textContent = phrase.frase;
    document.getElementById('translation').textContent = phrase.traducao;
    document.getElementById('translation').classList.add('hidden');
}

// Mostra tradução
document.getElementById('show-translation').addEventListener('click', () => {
    document.getElementById('translation').classList.remove('hidden');
});

// Toca áudio
function playAudio(speed) {
    const audioPath = phrases[currentPhrase].audio_normal;
    const audio = new Audio(audioPath);
    audio.playbackRate = speed;
    audio.play();
}

// Valida pronúncia com precisão selecionada
document.getElementById('start-pronunciation').addEventListener('click', async () => {
    const button = document.getElementById('start-pronunciation');
    button.disabled = true;
    button.textContent = 'Ouvindo...';

    try {
        const correctPhrase = phrases[currentPhrase].frase;
        const isCorrect = await validatePronunciation(correctPhrase, currentAccuracy);
        
        if (isCorrect) {
            alert('Pronúncia correta! Próxima frase...');
            currentPhrase = (currentPhrase + 1) % phrases.length;
            showPhrase();
        } else {
            alert(`Pronúncia abaixo de ${currentAccuracy}%. Tente novamente!`);
        }
    } catch (error) {
        alert(error); // Erro no microfone
    } finally {
        button.disabled = false;
        button.textContent = 'Start Your Pronunciation';
    }
});

// Atualiza o nível de precisão
document.getElementById('accuracy-select').addEventListener('change', (e) => {
    currentAccuracy = parseInt(e.target.value);
});

// Inicializa o quiz
loadPhrases();
