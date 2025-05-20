function initVoiceRecognition() {
  if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
    console.log('Reconocimiento de voz no soportado');
    voiceToggleBtn.style.display = 'none';
    return false;
  }
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  recognition = new SpeechRecognition();
  recognition.continuous = true;
  recognition.interimResults = true;
  recognition.lang = 'es-ES';

  recognition.onstart = () => console.log('🎤 VoiceRecognition iniciado');
  recognition.onend   = () => {
    console.log('🎤 VoiceRecognition finalizado');
    if (voiceActive) {
      // reinicia si se cerró por timeout
      recognition.start();
    }
  };
  recognition.onerror = (event) => {
    console.error('Error de reconocimiento:', event.error);
    showVoiceNotification('Error de voz: ' + event.error, 'error');
    // opcional: detener o reiniciar
    voiceActive = false;
    voiceStatus.textContent = 'Activar';
    voiceToggleBtn.classList.remove('active');
  };
  recognition.onresult = (event) => {
    let finalTranscript = '';
    for (let i = event.resultIndex; i < event.results.length; i++) {
      if (event.results[i].isFinal) {
        finalTranscript += event.results[i][0].transcript;
      }
    }
    if (finalTranscript) {
      const transcript = finalTranscript.toLowerCase().trim();
      console.log('Escuchado:', transcript);
      const keywords = ['buenas', 'loteria', 'pause', 'pausa'];
      if (keywords.some(k => transcript.includes(k)) && jugando && !pausa) {
        console.log('Palabra clave detectada:', transcript);
        pauseGame();
        showVoiceNotification('¡Juego pausado por voz! 🎤⏸️');
        playNotificationSound();
      }
    }
  };

  // Atacha el listener al botón (si no lo tienes ya)
  voiceToggleBtn.addEventListener('click', () => toggleVoiceRecognition());
  return true;
}
