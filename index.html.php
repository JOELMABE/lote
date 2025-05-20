<?php
// lote.php - ¡Lotería Mexicana Deluxe! 🌮🎉
// Versión mejorada con diseño moderno, responsive y reconocimiento de voz

$cartas = [
    'EL GALLO', 'EL DIABLO', 'LA DAMA', 'EL CATRIN', 'EL PARAGUAS',
    'LA SIRENA', 'LA ESCALERA', 'LA BOTELLA', 'EL BARRIL', 'EL ARBOL',
    'EL MELON', 'EL VALIENTE', 'EL GORRITO', 'LA MUERTE', 'LA PERA',
    'LA BANDERA', 'EL BANDOLON', 'EL VIOLONCELLO', 'LA GARZA', 'EL PAJARO',
    'LA MANO', 'LA BOTA', 'LA LUNA', 'EL COTORRO', 'EL BORRACHO',
    'EL NEGRITO', 'EL CORAZON', 'LA SANDIA', 'EL TAMBOR', 'EL CAMARON',
    'LAS JARAS', 'EL MUSICO', 'LA ARAÑA', 'EL SOLDADO', 'LA ESTRELLA',
    'EL CAZO', 'EL MUNDO', 'EL APACHE', 'EL NOPAL', 'EL ALACRAN',
    'LA ROSA', 'LA CALAVERA', 'LA CAMPANA', 'EL CANTARITO', 'EL VENADO',
    'EL SOL', 'LA CORONA', 'LA CHALUPA', 'EL PINO', 'EL PESCADO',
    'LA PALMA', 'LA MACETA', 'EL ARPA', 'LA RANA'
];

function remove_accents($string) {
    $unwanted_array = [
        'Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ó'=>'O', 'Ú'=>'U',
        'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
        'Ñ'=>'N', 'ñ'=>'n', ' '=>'_', '¿'=>'?', '¡'=>'!'
    ];
    return strtr($string, $unwanted_array);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎴 Lotería Mexicana Deluxe</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Fredoka+One&family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles-enhanced.css">
    <link rel="stylesheet" href="A.js">
    
</head>
<body>
    <div class="container">
     <header class="animate-fade mexican-theme">
    <h1 class="floating">Lotería Mexicana <span class="cecytej-logo">🎴</span></h1>
    <p class="subtitle">¡La versión más divertida del juego en Cecytej Cuquío!</p>
    <img src="cecytej.png" class="cecytej-logo" alt="Logo CECyTEJ">
</header>
        
        <div class="game-area">
            <div class="left-panel animate-fade" style="animation-delay: 0.3s;">
                <div class="speed-control floating">
                    <label for="speed-slider"><i class="fas fa-tachometer-alt"></i> Velocidad de las cartas:</label>
                    <input type="range" id="speed-slider" min="0.5" max="3" step="0.5" value="1.5">
                    <div class="speed-value"><span id="speed-label">1.5</span> segundos</div>
                </div>

                <div class="voice-control floating">
                    <label><i class="fas fa-microphone"></i> Reconocimiento de voz:</label>
                    <button id="voice-toggle-btn" class="voice-btn" title="Activar/Desactivar reconocimiento de voz">
                        <i class="fas fa-microphone"></i> <span id="voice-status">Activar</span>
                    </button>
                    <div id="voice-indicator" class="voice-indicator">
                        <span>🎤 Escuchando...</span>
                    </div>
                    <div class="voice-help">
                        <small>Di: "buenas", "loteria" o "pause" para pausar</small>
                    </div>
                </div>
                
                <div class="controls">
                    <button id="start-btn" class="success pulse"><i class="fas fa-play"></i> Comenzar</button>
                    <button id="pause-btn" class="secondary"><i class="fas fa-pause"></i> Pausar</button>
                    <button id="verify-btn"><i class="fas fa-search"></i> Verificar</button>
                    <button id="restart-btn" class="warning"><i class="fas fa-redo"></i> Reiniciar</button>
                    <button id="toggle-theme-btn" title="Cambiar tema"><i class="fas fa-adjust"></i> Tema</button>
                    <button id="fullscreen-btn" title="Pantalla completa"><i class="fas fa-expand"></i> Pantalla</button>
                </div>
            </div>
            
            <div class="center-panel animate-fade" style="animation-delay: 0.2s;">
                <div class="card-display floating">
                    <div id="card-image"></div>
                    <div id="card-name">¡Presiona "Comenzar" para iniciar el juego!</div>
                    <div class="card-info">
                        <span id="card-count">Cartas mostradas: 0</span>
                        <span id="remaining-cards">Cartas restantes: <?php echo count($cartas); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="right-panel">
                <div id="thumbnails" class="animate-fade" style="animation-delay: 0.5s;"></div>
            </div>
        </div>
    </div>
    
    <div id="missing-cards-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span><i class="fas fa-search"></i> Cartas Faltantes</span>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div id="missing-cards-list"></div>
            </div>
        </div>
    </div>

    <script>
        const cartas = <?php echo json_encode($cartas); ?>;
        let cartasBarajadas = [];
        let indice = 0;
        let jugando = false;
        let pausa = false;
        let intervalo;
        let speed = 1.5;

        let recognition;
        let voiceActive = false;

        const cardImageDiv = document.getElementById('card-image');
        const cardNameDiv = document.getElementById('card-name');
        const cardCountDiv = document.getElementById('card-count');
        const remainingCardsDiv = document.getElementById('remaining-cards');
        const startBtn = document.getElementById('start-btn');
        const pauseBtn = document.getElementById('pause-btn');
        const verifyBtn = document.getElementById('verify-btn');
        const restartBtn = document.getElementById('restart-btn');
        const missingCardsModal = document.getElementById('missing-cards-modal');
        const missingCardsList = document.getElementById('missing-cards-list');
        const modalCloseBtn = document.querySelector('.modal-close');
        const speedSlider = document.getElementById('speed-slider');
        const speedLabel = document.getElementById('speed-label');
        const thumbnailsDiv = document.getElementById('thumbnails');
        const voiceToggleBtn = document.getElementById('voice-toggle-btn');
        const voiceStatus = document.getElementById('voice-status');
        const voiceIndicator = document.getElementById('voice-indicator');

        function removeAccents(str) {
            const accents = {'Á':'A','É':'E','Í':'I','Ó':'O','Ú':'U','á':'a','é':'e','í':'i','ó':'o','ú':'u','Ñ':'N','ñ':'n',' ':'_'};
            return str.split('').map(c => accents[c] || c).join('');
        }

        function initVoiceRecognition() {
            if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                recognition = new SpeechRecognition();
                
                recognition.continuous = true;
                recognition.interimResults = true;
                recognition.lang = 'es-ES';
                recognition.maxAlternatives = 1;

                recognition.onstart = () => {
                    voiceIndicator.style.display = 'block';
                    voiceIndicator.classList.add('listening');
                };

                recognition.onend = () => {
                    voiceIndicator.style.display = 'none';
                    voiceIndicator.classList.remove('listening');
                    
                    if (voiceActive) {
                        setTimeout(() => {
                            try {
                                recognition.start();
                            } catch (e) {
                                console.log('Voice recognition restart failed:', e);
                                toggleVoiceRecognition(false);
                            }
                        }, 500);
                    }
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
                        const foundKeyword = keywords.some(keyword => transcript.includes(keyword));
                        
                        if (foundKeyword && jugando && !pausa) {
                            console.log('Palabra clave detectada:', transcript);
                            pauseGame();
                            showVoiceNotification('¡Juego pausado por voz! 🎤⏸️');
                            playNotificationSound();
                        }
                    }
                };

                recognition.onerror = (event) => {
                    console.error('Error en reconocimiento de voz:', event.error);
                    if (event.error === 'not-allowed') {
                        showVoiceNotification('Permisos de micrófono denegados', 'error');
                        toggleVoiceRecognition(false);
                    }
                };

                return true;
            } else {
                console.log('Reconocimiento de voz no soportado');
                voiceToggleBtn.style.display = 'none';
                return false;
            }
        }

        function toggleVoiceRecognition(activate = null) {
            if (activate !== null) {
                voiceActive = activate;
            } else {
                voiceActive = !voiceActive;
            }
            
            if (voiceActive) {
                try {
                    recognition.start();
                    voiceStatus.textContent = 'Desactivar';
                    voiceToggleBtn.classList.add('active');
                    showVoiceNotification('Reconocimiento de voz activado 🎤');
                } catch (e) {
                    console.error('Error al iniciar reconocimiento:', e);
                    voiceActive = false;
                    voiceStatus.textContent = 'Activar';
                    voiceToggleBtn.classList.remove('active');
                }
            } else {
                if (recognition) {
                    recognition.stop();
                }
                voiceStatus.textContent = 'Activar';
                voiceToggleBtn.classList.remove('active');
                voiceIndicator.style.display = 'none';
                showVoiceNotification('Reconocimiento de voz desactivado');
            }
        }

        function pauseGame() {
            if (!jugando || pausa) return;
            
            pausa = true;
            pauseBtn.innerHTML = '<i class="fas fa-play"></i> Reanudar';
            pauseBtn.className = 'success';
            clearInterval(intervalo);
            cardNameDiv.textContent = 'Juego pausado por voz 🎤⏸️';
        }

        function showVerificationModal() {
            const mostradas = new Set(cartasBarajadas.slice(0, indice));
            const faltantes = cartas.filter(carta => !mostradas.has(carta));
            
            missingCardsList.innerHTML = faltantes.length ? 
                faltantes.map(carta => {
                    const index = cartas.indexOf(carta);
                    const fileName = (index + 1) + '_' + removeAccents(carta) + '.jpg';
                    return `<img src="${fileName}" alt="${carta}" title="${carta}">`;
                }).join('') : 
                '<p style="text-align: center; font-weight: 600; color: var(--success); font-size: 1.2rem; padding: 20px;"><i class="fas fa-check-circle"></i> ¡Todas las cartas han salido! 🎉</p>';
            
            missingCardsModal.style.display = 'flex';
            setTimeout(() => {
                missingCardsModal.classList.add('show');
            }, 10);
        }

        function showVoiceNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.backgroundColor = type === 'error' ? 'var(--secondary)' : 'var(--primary)';
            notification.style.color = 'white';
            notification.style.padding = '15px 25px';
            notification.style.borderRadius = '10px';
            notification.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
            notification.style.fontWeight = '600';
            notification.style.zIndex = '1500';
            notification.style.animation = 'slideInRight 0.5s ease-out';
            notification.style.maxWidth = '300px';
            notification.innerHTML = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideInRight 0.5s ease-out reverse forwards';
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        }

        function playNotificationSound() {
            if ('AudioContext' in window || 'webkitAudioContext' in window) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                const audioContext = new AudioContext();
                
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime + 0.2);
                
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.3);
            }
        }

        speedSlider.addEventListener('input', (e) => {
            speed = parseFloat(e.target.value);
            speedLabel.textContent = speed;
            
            if (jugando && !pausa) {
                clearInterval(intervalo);
                intervalo = setInterval(mostrarCarta, speed * 1000);
            }
        });

        voiceToggleBtn.addEventListener('click', () => {
            toggleVoiceRecognition();
        });

        function crearConfetti() {
            const colors = ['#8A2BE2', '#FF6B6B', '#FFD166', '#06D6A0', '#118AB2', '#FF9F1C'];
            
            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.top = -10 + 'px';
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = Math.random() * 10 + 5 + 'px';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animation = `confettiFall ${Math.random() * 3 + 2}s linear forwards`;
                confetti.style.animationDelay = Math.random() * 0.5 + 's';
                document.body.appendChild(confetti);
                
                setTimeout(() => confetti.remove(), 5000);
            }
        }

        function mostrarCarta() {
            if (!jugando || pausa) return;
            
            cardImageDiv.style.animation = 'cardFlipIn 0.5s ease-out forwards';
            cardNameDiv.style.opacity = 0;
            
            setTimeout(() => {
                if (indice >= cartasBarajadas.length) {
                    finalizarJuego();
                    return;
                }
                
                const carta = cartasBarajadas[indice];
                const index = cartas.indexOf(carta);
                const fileName = (index + 1) + '_' + removeAccents(carta) + '.jpg';
                const imgPath =  fileName;

                cardImageDiv.style.backgroundImage = `url(${imgPath})`;
                cardNameDiv.textContent = carta;
                cardCountDiv.textContent = `Cartas mostradas: ${indice + 1}`;
                remainingCardsDiv.textContent = `Cartas restantes: ${cartas.length - (indice + 1)}`;
                cardNameDiv.style.opacity = 1;
                cardImageDiv.style.animation = '';

                const imgThumb = document.createElement('img');
                imgThumb.src = imgPath;
                imgThumb.alt = carta;
                imgThumb.title = carta;
                imgThumb.style.opacity = '0';
                imgThumb.style.transform = 'translateY(20px)';
                thumbnailsDiv.appendChild(imgThumb);
                
                setTimeout(() => {
                    imgThumb.style.opacity = '1';
                    imgThumb.style.transform = 'translateY(0)';
                    thumbnailsDiv.scrollTop = thumbnailsDiv.scrollHeight;
                }, 50);

                if ('speechSynthesis' in window) {
                    const utterance = new SpeechSynthesisUtterance(carta);
                    utterance.lang = 'es-ES';
                    utterance.rate = 0.9;
                    window.speechSynthesis.cancel();
                    window.speechSynthesis.speak(utterance);
                }

                indice++;
            }, 200);
        }

        function finalizarJuego() {
            clearInterval(intervalo);
            jugando = false;
            cardNameDiv.textContent = '¡Juego terminado! Todas las cartas mostradas 🎉';
            crearConfetti();
            
            const notification = document.createElement('div');
            notification.style.position = 'fixed';
            notification.style.bottom = '20px';
            notification.style.left = '50%';
            notification.style.transform = 'translateX(-50%)';
            notification.style.backgroundColor = 'var(--success)';
            notification.style.color = 'white';
            notification.style.padding = '15px 30px';
            notification.style.borderRadius = '50px';
            notification.style.boxShadow = '0 10px 25px rgba(6, 214, 160, 0.3)';
            notification.style.fontWeight = '600';
            notification.style.zIndex = '1000';
            notification.style.animation = 'fadeInUp 0.5s ease-out';
            notification.innerHTML = '<i class="fas fa-check-circle"></i> ¡Todas las cartas se han mostrado!';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'fadeInUp 0.5s ease-out reverse forwards';
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        }

        startBtn.addEventListener('click', () => {
            cartasBarajadas = [...cartas].sort(() => Math.random() - 0.5);
            indice = 0;
            jugando = true;
            pausa = false;
            pauseBtn.innerHTML = '<i class="fas fa-pause"></i> Pausar';
            pauseBtn.className = 'secondary';
            thumbnailsDiv.innerHTML = '';
            cardCountDiv.textContent = 'Cartas mostradas: 0';
            remainingCardsDiv.textContent = `Cartas restantes: ${cartas.length}`;
            cardNameDiv.textContent = '¡Que comience el juego! 🎮';
            startBtn.classList.remove('pulse');
            
            cardImageDiv.style.animation = 'pulse 1s ease';
            setTimeout(() => cardImageDiv.style.animation = '', 1000);
            
            crearConfetti();
            
            clearInterval(intervalo);
            intervalo = setInterval(mostrarCarta, speed * 1000);
        });

        pauseBtn.addEventListener('click', () => {
            if (!jugando) return;
            
            pausa = !pausa;
            pauseBtn.innerHTML = pausa ? '<i class="fas fa-play"></i> Reanudar' : '<i class="fas fa-pause"></i> Pausar';
            pauseBtn.className = pausa ? 'success' : 'secondary';
            
            if (pausa) {
                clearInterval(intervalo);
                cardNameDiv.textContent = 'Juego en pausa ⏸️';
            } else {
                intervalo = setInterval(mostrarCarta, speed * 1000);
                cardNameDiv.textContent = '¡Juego reanudado! ▶️';
            }
        });

        restartBtn.addEventListener('click', () => {
            if (!jugando) return;
            
            const confirmModal = document.createElement('div');
            confirmModal.style.position = 'fixed';
            confirmModal.style.top = '0';
            confirmModal.style.left = '0';
            confirmModal.style.width = '100%';
            confirmModal.style.height = '100%';
            confirmModal.style.backgroundColor = 'rgba(0,0,0,0.7)';
            confirmModal.style.display = 'flex';
            confirmModal.style.justifyContent = 'center';
            confirmModal.style.alignItems = 'center';
            confirmModal.style.zIndex = '2000';
            
            confirmModal.innerHTML = `
                <div style="background: white; border-radius: 20px; padding: 30px; max-width: 400px; text-align: center; box-shadow: 0 15px 30px rgba(0,0,0,0.2); animation: fadeInUp 0.3s ease-out">
                    <h3 style="margin-bottom: 20px; color: var(--dark); font-size: 1.3rem;"><i class="fas fa-question-circle" style="color: var(--warning);"></i> ¿Reiniciar el juego?</h3>
                    <p style="margin-bottom: 25px;">Se perderá el progreso actual. ¿Estás seguro?</p>
                    <div style="display: flex; gap: 15px; justify-content: center;">
                        <button id="confirm-restart" style="background: var(--secondary); color: white; border: none; padding: 10px 25px; border-radius: 50px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;"><i class="fas fa-check"></i> Sí</button>
                        <button id="cancel-restart" style="background: var(--dark-light); color: white; border: none; padding: 10px 25px; border-radius: 50px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;"><i class="fas fa-times"></i> No</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(confirmModal);
            
            document.getElementById('confirm-restart').addEventListener('click', () => {
                clearInterval(intervalo);
                cartasBarajadas = [...cartas].sort(() => Math.random() - 0.5);
                indice = 0;
                pausa = false;
                pauseBtn.innerHTML = '<i class="fas fa-pause"></i> Pausar';
                pauseBtn.className = 'secondary';
                thumbnailsDiv.innerHTML = '';
                cardCountDiv.textContent = 'Cartas mostradas: 0';
                remainingCardsDiv.textContent = `Cartas restantes: ${cartas.length}`;
                cardNameDiv.textContent = '¡Juego reiniciado! 🔄';
                crearConfetti();
                
                intervalo = setInterval(mostrarCarta, speed * 1000);
                confirmModal.remove();
            });
            
            document.getElementById('cancel-restart').addEventListener('click', () => {
                confirmModal.remove();
            });
        });

        verifyBtn.addEventListener('click', () => {
            if (!jugando) {
                const errorNotification = document.createElement('div');
                errorNotification.style.position = 'fixed';
                errorNotification.style.bottom = '20px';
                errorNotification.style.left = '50%';
                errorNotification.style.transform = 'translateX(-50%)';
                errorNotification.style.backgroundColor = 'var(--secondary)';
                errorNotification.style.color = 'white';
                errorNotification.style.padding = '15px 30px';
                errorNotification.style.borderRadius = '50px';
                errorNotification.style.boxShadow = '0 10px 25px rgba(255, 107, 107, 0.3)';
                errorNotification.style.fontWeight = '600';
                errorNotification.style.zIndex = '1000';
                errorNotification.style.animation = 'fadeInUp 0.5s ease-out';
                errorNotification.innerHTML = '<i class="fas fa-exclamation-circle"></i> ¡Primero inicia el juego! 🎮';
                document.body.appendChild(errorNotification);
                
                setTimeout(() => {
                    errorNotification.style.animation = 'fadeInUp 0.5s ease-out reverse forwards';
                    setTimeout(() => errorNotification.remove(), 500);
                }, 3000);
                return;
            }
            
            showVerificationModal();
        });

        modalCloseBtn.addEventListener('click', () => {
            missingCardsModal.classList.remove('show');
            setTimeout(() => {
                missingCardsModal.style.display = 'none';
            }, 300);
        });

        missingCardsModal.addEventListener('click', (e) => {
            if (e.target === missingCardsModal) {
                missingCardsModal.classList.remove('show');
                setTimeout(() => {
                    missingCardsModal.style.display = 'none';
                }, 300);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            initVoiceRecognition();
            
            const elements = document.querySelectorAll('.animate-fade');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1 + 0.2}s`;
            });
            
            setTimeout(() => {
                startBtn.classList.add('pulse');
            }, 1000);
            
            thumbnailsDiv.addEventListener('mouseover', (e) => {
                if (e.target.tagName === 'IMG') {
                    e.target.style.transform = 'scale(1.15) rotate(3deg)';
                }
            });
            
            thumbnailsDiv.addEventListener('mouseout', (e) => {
                if (e.target.tagName === 'IMG') {
                    e.target.style.transform = 'scale(1)';
                }
            });

            const toggleThemeBtn = document.getElementById('toggle-theme-btn');
            toggleThemeBtn.addEventListener('click', () => {
                document.body.classList.toggle('dark-theme');
            });

            const fullscreenBtn = document.getElementById('fullscreen-btn');
            fullscreenBtn.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            });
        });
    </script>

    <audio id="maraca-sound" src="maraca-shake.mp3"></audio>
    <script>
        document.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                const maraca = document.getElementById('maraca-sound');
                maraca.currentTime = 0;
                maraca.play();
            });
        });

        function showVictoryAnimation() {
            const victoryMessages = [
                "¡Órale!", 
                "¡Ándale!", 
                "¡Eso es!", 
                "¡Sí señor!", 
                "¡Arriba México!"
            ];
            
            const message = victoryMessages[Math.floor(Math.random() * victoryMessages.length)];
            const victoryDiv = document.createElement('div');
            victoryDiv.style.position = 'fixed';
            victoryDiv.style.top = '50%';
            victoryDiv.style.left = '50%';
            victoryDiv.style.transform = 'translate(-50%, -50%)';
            victoryDiv.style.fontSize = '3em';
            victoryDiv.style.color = '#e74c3c';
            victoryDiv.style.textShadow = '3px 3px 0 #ffd700';
            victoryDiv.style.animation = 'pulse 1s infinite';
            victoryDiv.textContent = message;
            
            document.body.appendChild(victoryDiv);
            
            setTimeout(() => {
                victoryDiv.style.animation = 'flyCard 2s ease-out forwards';
                setTimeout(() => victoryDiv.remove(), 2000);
            }, 1000);
        }
    </script>

    <style>
        .voice-control {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .voice-control label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .voice-btn {
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .voice-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }

        .voice-btn.active {
            background: linear-gradient(45deg, #06d6a0, #00b894);
            box-shadow: 0 4px 15px rgba(6, 214, 160, 0.3);
            animation: pulse 2s infinite;
        }

        .voice-indicator {
            display: none;
            text-align: center;
            margin-top: 10px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            backdrop-filter: blur(10px);
        }

        .voice-indicator.listening {
            animation: voicePulse 1.5s infinite;
        }

        .voice-help {
            margin-top: 10px;
            text-align: center;
            font-style: italic;
            opacity: 0.9;
        }

        @keyframes voicePulse {
            0%, 100% { 
                opacity: 0.7; 
                transform: scale(1);
            }
            50% { 
                opacity: 1; 
                transform: scale(1.05);
            }
        }

        @keyframes slideInRight {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes cardFlipIn {
            0% {
                transform: rotateY(-90deg);
                opacity: 0;
            }
            100% {
                transform: rotateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .voice-control {
                margin-bottom: 15px;
                padding: 15px;
            }
            
            .voice-btn {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .voice-help small {
                font-size: 0.8rem;
            }
        }
    </style>


</body>
</html>