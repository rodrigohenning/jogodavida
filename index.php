<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo da Vida com Cobrinha</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="grid" id="grid"></div>
    <script>
        const width = 128;
        const height = 128;
        const grid = document.getElementById('grid');

        // Inicializa a grade
        function initializeGrid() {
            const fragment = document.createDocumentFragment();
            for (let y = 0; y < height; y++) {
                for (let x = 0; x < width; x++) {
                    const cell = document.createElement('div');
                    cell.classList.add('cell', 'dead');
                    cell.dataset.x = x;
                    cell.dataset.y = y;
                    fragment.appendChild(cell);
                }
            }
            grid.appendChild(fragment);
        }

        // Atualiza a grade com base no tabuleiro
        function updateGrid(board) {
            const cells = document.querySelectorAll('.cell');
            cells.forEach(cell => {
                const x = parseInt(cell.dataset.x);
                const y = parseInt(cell.dataset.y);
                if (board[y][x] === 1) {
                    cell.classList.add('alive');
                    cell.classList.remove('dead');
                } else if (board[y][x] === 2) {
                    cell.classList.add('snake');
                    cell.classList.remove('alive', 'dead');
                } else {
                    cell.classList.add('dead');
                    cell.classList.remove('alive', 'snake');
                }
            });
        }

        // Função para atualizar o tabuleiro
        async function fetchAndUpdateBoard(board) {
            const response = await fetch('game_of_life.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `board=${JSON.stringify(board)}`,
            });
            const newBoard = await response.json();
            return newBoard;
        }

        // Função para executar o jogo
        async function runGame() {
            let board = await fetch('game_of_life.php?initialize=true').then(response => response.json());
            updateGrid(board);

            setInterval(async () => {
                board = await fetchAndUpdateBoard(board);
                updateGrid(board);
            }, 100);
        }

        initializeGrid();
        runGame();
		
		
    </script>
</body>
</html>
