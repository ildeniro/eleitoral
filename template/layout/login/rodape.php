</div>

<!-- jQuery  -->
<script src="<?= PORTAL_URL; ?>template/assets/libs/js/jquery-1.9.1.min.js" type="text/javascript"></script>
<!-- Livequery -->
<script src="<?= PORTAL_URL; ?>template/assets/libs/js/livequery.js" type="text/javascript"></script>
<script src="<?= PORTAL_URL; ?>template/assets/libs/js/livequery.js" type="text/javascript"></script>
<!-- base:js -->
<script src="<?= PORTAL_URL; ?>template/vendors/js/vendor.bundle.base.js" type="text/javascript"></script>
<!-- Utils Js -->
<script type="text/javascript" src="<?= PORTAL_URL; ?>config/geral.js" type="text/javascript"></script>
<!--<script src="<?= PORTAL_URL; ?>template/assets/vendor/jquery/jquery-3.3.1.min.js" type="text/javascript" ></script>-->
<script src="<?= PORTAL_URL; ?>template/assets/vendor/bootstrap/js/bootstrap.bundle.js" type="text/javascript"></script>
<!-- Sweet-Alert  -->
<script src="<?= PORTAL_URL; ?>template/assets/vendor/sweetalert/sweetalert2.min.js"></script>
<script>
    const canvas = document.getElementById('background-login');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    let particlesArray;

    // Definindo os atributos das partículas
    class Particle {
        constructor(x, y, directionX, directionY, size, color) {
            this.x = x;
            this.y = y;
            this.directionX = directionX;
            this.directionY = directionY;
            this.size = size;
            this.color = color;
        }

        // Método para desenhar a partícula
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2, false);
            ctx.fillStyle = this.color;
            ctx.fill();
        }

        // Atualiza a posição da partícula e garante que elas fiquem espalhadas
        update() {
            if (this.x > canvas.width || this.x < 0) {
                this.directionX = -this.directionX;
            }
            if (this.y > canvas.height || this.y < 0) {
                this.directionY = -this.directionY;
            }
            this.x += this.directionX;
            this.y += this.directionY;
            this.draw();
        }
    }

    // Conecta as partículas próximas para simular conexões
    function connectParticles() {
        let opacityValue = 1;
        for (let a = 0; a < particlesArray.length; a++) {
            for (let b = a + 1; b < particlesArray.length; b++) {
                let distance = ((particlesArray[a].x - particlesArray[b].x) * (particlesArray[a].x - particlesArray[b].x)) +
                    ((particlesArray[a].y - particlesArray[b].y) * (particlesArray[a].y - particlesArray[b].y));
                if (distance < 8000) { // Aumenta a distância máxima para a conexão
                    opacityValue = 1 - (distance / 8000); // Quanto mais próximas, mais opacas
                    ctx.strokeStyle = `rgba(252, 163, 17, ${opacityValue})`;
                    ctx.lineWidth = 0.5;
                    ctx.beginPath();
                    ctx.moveTo(particlesArray[a].x, particlesArray[a].y);
                    ctx.lineTo(particlesArray[b].x, particlesArray[b].y);
                    ctx.stroke();
                }
            }
        }
    }

    // Inicializa as partículas, distribuindo-as de forma mais uniforme e aleatória
    function init() {
        particlesArray = [];
        let numberOfParticles = (canvas.height * canvas.width) / 5000; // Ajusta o número de partículas
        for (let i = 0; i < numberOfParticles; i++) {
            let size = Math.random() * 2 + 1; // Partículas menores
            let x = Math.random() * canvas.width; // Distribui as partículas por toda a largura
            let y = Math.random() * canvas.height; // Distribui as partículas por toda a altura
            let directionX = (Math.random() * 1) - 0.5; // Movimento aleatório horizontal
            let directionY = (Math.random() * 1) - 0.5; // Movimento aleatório vertical
            let color = '#FCA311'; // Laranja opaco
            particlesArray.push(new Particle(x, y, directionX, directionY, size, color));
        }
    }

    // Função de animação
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (let i = 0; i < particlesArray.length; i++) {
            particlesArray[i].update();
        }
        connectParticles(); // Conectar partículas próximas
        requestAnimationFrame(animate);
    }

    // Ajusta o canvas conforme a janela redimensiona
    window.addEventListener('resize', function() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        init(); // Reinicializa as partículas no novo tamanho
    });

    // Inicia a animação
    init();
    animate();
</script>
</body>

</html>