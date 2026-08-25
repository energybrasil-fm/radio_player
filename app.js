// Registra o Service Worker para habilitar recursos PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('sw.js')
            .then(registration => {
                console.log('Service Worker registrado com sucesso:', registration.scope);
            })
            .catch(error => {
                console.log('Falha ao registrar o Service Worker:', error);
            });
    });
}

// Lógica do Player de Rádio
const audio = document.getElementById('radioStream');
const playPauseBtn = document.getElementById('playPauseBtn');
const status = document.getElementById('status');
let isPlaying = false;

playPauseBtn.addEventListener('click', () => {
    if (isPlaying) {
        audio.pause();
        playPauseBtn.textContent = 'Tocar';
        status.textContent = 'Pausado';
    } else {
        status.textContent = 'Carregando stream...';
        audio.play().then(() => {
            playPauseBtn.textContent = 'Pausar';
            status.textContent = 'Tocando agora';
        }).catch(e => {
            status.textContent = 'Erro ao carregar o Ã¡udio';
            console.error('Erro de reproduÃ§Ã£o:', e);
        });
    }
    isPlaying = !isPlaying;
});
