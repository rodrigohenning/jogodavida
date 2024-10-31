<?php
function initializeBoard($width, $height) {
    $board = [];
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $board[$y][$x] = rand(0, 1); // 0: morta, 1: viva
        }
    }
    // Inicializa a cobrinha
    $board[0][0] = 2; // Posição inicial da cobrinha
    return $board;
}

function getLivingNeighbors($board, $x, $y) {
    $directions = [
        [-1, -1], [-1, 0], [-1, 1],
        [0, -1],         [0, 1],
        [1, -1], [1, 0], [1, 1]
    ];
    $livingNeighbors = 0;

    foreach ($directions as $direction) {
        $newX = $x + $direction[0];
        $newY = $y + $direction[1];
        if (isset($board[$newY][$newX]) && $board[$newY][$newX] == 1) {
            $livingNeighbors++;
        }
    }
    return $livingNeighbors;
}

function updateBoard($board) {
    $newBoard = [];
    $height = count($board);
    $width = count($board[0]);

    // Inicializa o novo tabuleiro
    for ($y = 0; $y < $height; $y++) {
        $newBoard[$y] = array_fill(0, $width, 0);
    }

    // Atualiza o Jogo da Vida
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $livingNeighbors = getLivingNeighbors($board, $x, $y);
            if ($board[$y][$x] == 1) {
                if ($livingNeighbors < 2 || $livingNeighbors > 3) {
                    $newBoard[$y][$x] = 0;
                } else {
                    $newBoard[$y][$x] = 1;
                }
            } else {
                if ($livingNeighbors == 3) {
                    $newBoard[$y][$x] = 1;
                } else {
                    $newBoard[$y][$x] = 0;
                }
            }
        }
    }

    // Atualiza a cobrinha
    $snakeHead = findSnakeHead($board);
    if ($snakeHead) {
        $newHead = moveSnake($snakeHead, $newBoard);
        $newBoard[$newHead[1]][$newHead[0]] = 2;
    }

    return $newBoard;
}

function findSnakeHead($board) {
    for ($y = 0; $y < count($board); $y++) {
        for ($x = 0; $x < count($board[0]); $x++) {
            if ($board[$y][$x] == 2) {
                return [$x, $y];
            }
        }
    }
    return null;
}

function moveSnake($head, &$board) {
    $directions = [
        [0, 1],  // direita
        [1, 0],  // abaixo
        [0, -1], // esquerda
        [-1, 0]  // acima
    ];
    foreach ($directions as $direction) {
        $newX = $head[0] + $direction[0];
        $newY = $head[1] + $direction[1];
        if (isset($board[$newY][$newX])) {
            return [$newX, $newY];
        }
    }
    return $head;
}

function boardToJson($board) {
    return json_encode($board);
}

$width = 128;
$height = 128;

if (isset($_GET['initialize']) && $_GET['initialize'] == 'true') {
    $board = initializeBoard($width, $height);
} else {
    $board = json_decode($_POST['board'], true);
    $board = updateBoard($board);
}

header('Content-Type: application/json');
echo boardToJson($board);
?>
