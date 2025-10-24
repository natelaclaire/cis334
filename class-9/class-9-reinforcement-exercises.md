
# Exercise 9-1 — Split the codebase + bootstrap (no DB yet)

## Goal

Refactor the one-file PHP script into a tiny MVC-ish layout where each class lives in its own file, there’s an `index.php` controller, and all game state continues to travel via **hidden inputs and the query string** (no sessions yet).

## Steps

1. **Make folders**

Create a new folder inside the `public/exercises` directory named `9-chess`. Inside it, create this structure:

```
public/exercises/9-chess/src/Domain/      # Chess domain classes (Board, Piece, Move, GameEngine, etc.)
public/exercises/9-chess/src/Infra/       # Repositories (empty for now)
public/exercises/9-chess/autoload.php     # simple class autoloader
```

2. **Move each class** out of `3-3-chess.php` into `public/exercises/9-chess/src/Domain/*`. Keep class names as-is; fix `namespace` (e.g., `namespace App\Domain;`) and `use` statements.

3. **Add a minimal autoloader** (`/autoload.php`):

```php
<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;

    $rel = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $rel) . '.php';
    if (is_file($file)) require $file;
});
```

4. **Create public entry points**

* `public/exercises/9-chess/index.php` – will be the home screen, leave blank for now.
* `public/exercises/9-chess/start.php` – will be our game starting logic, leave blank for now.
* `public/exercises/9-chess/play.php` – move the HTML page from `3-3-chess.php` into this file and add a PHP block at the top:

```php
<?php
require __DIR__ . '/autoload.php';
use App\Domain\{ChessBoard, ChessPiece};
?>
```

## Done when…

* The game runs exactly as before from `public/exercises/9-chess/play.php`.
* No database code yet.

---

# Exercise 9-2 — Add PDO + Database class + player selection from DB

## Goal

Add a `Database` class that provides a static PDO connection. Create a `Player` class and a `PlayerRepository` that reads active players from the `players` table. Update the start screen to let users pick two players from the database before starting a game.

## Steps

1. **Create `Database` class** with a **static PDO**:

```php
<?php
namespace App;

use PDO;

final class Database {
    private static ?PDO $pdo = null;

    public static function get(): PDO {
        if (!self::$pdo) {
            $dsn  = "mysql:host=db;dbname=mydb;charset=utf8mb4";
            $user = "mariadb"; $pass = "mariadb";
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }
}
```

2. **Create `Player` class** and `PlayerRepository` that reads from `players`:

* Columns available: `id`, `username`, `email`, `is_active`, ratings/stats, etc. 

```php
namespace App\Domain;

final class Player {
    public int $id;
    public string $username;
    public ?string $email;
    public string $fullName;
    public string $createdAt;
    public string $updatedAt;
    public bool $isActive;
    public int $totalGames;
    public int $wins;
    public int $losses;
    public int $draws;
    public int $rating;
}

namespace App\Infra;

use App\Database;
use App\Domain\Player;
use PDO;

final class PlayerRepository {
    /** @return Player[] */
    public function allActive(): array {
        $sql = "SELECT 
                    id, 
                    username, 
                    email, 
                    full_name as fullName, 
                    created_at as createdAt, 
                    updated_at as updatedAt, 
                    is_active as isActive, 
                    total_games as totalGames, 
                    wins, 
                    losses, 
                    draws, 
                    rating
                FROM players 
                WHERE is_active = 1 
                ORDER BY username";
        $st  = Database::get()->query($sql);
        return $st->fetchAll(PDO::FETCH_CLASS, Player::class);
    }

    public function findById(int $id): ?Player {
        $sql = "SELECT 
                    id, 
                    username, 
                    email, 
                    full_name as fullName, 
                    created_at as createdAt, 
                    updated_at as updatedAt, 
                    is_active as isActive, 
                    total_games as totalGames, 
                    wins, 
                    losses, 
                    draws, 
                    rating
                FROM players 
                WHERE id = :id";
        $st  = Database::get()->prepare($sql);
        $st->execute(['id' => $id]);
        $st->setFetchMode(PDO::FETCH_CLASS, Player::class);
        $player = $st->fetch();
        return $player === false ? null : $player;
    }
}
```

3. **Update `start.php`**:

* Query players via `PlayerRepository`.
* Render two `<select>` boxes (White, Black) populated from DB.
* When submitted (POST), **do not** create a DB record yet—still pass state via hidden fields and redirect to `play.php?whiteId=...&blackId=...`.
* Below is an example of rendering the form that you can copy and paste into `start.php`, or you can build it yourself:

```php
<?php
require __DIR__ . '/autoload.php';
use App\Domain\{ChessBoard, ChessPiece};
use App\Infra\PlayerRepository;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $whiteId = (int)$_POST['whiteId'];
    $blackId = (int)$_POST['blackId'];

    if ($whiteId === $blackId) {
        $message = 'Players must be different.';
    } else {
        $playerRepo = new PlayerRepository();
        $whitePlayer = $playerRepo->findById($whiteId);
        $blackPlayer = $playerRepo->findById($blackId);

        if (!$whitePlayer || !$blackPlayer) {
            $message = 'Invalid player selection.';
        } else {
            // Initialize a new chess board
            header('Location: play.php?whiteId=' . $whiteId . '&blackId=' . $blackId);
            exit;
        }
    }
    
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Chess Game</title>
	<link rel="stylesheet" href="/css/styles.css">
</head>
<body>
	<h1 style="text-align:center">Chess Game</h1>

    <?php if ($message): ?>
        <p style="color: red; text-align: center;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <form method="post" style="text-align: center; margin-top: 2em;">
        <label>White Player: <select name="whiteId">
            <?php
            // Fetch active players from the repository
            $playerRepo = new PlayerRepository();
            $players = $playerRepo->allActive();
            foreach ($players as $player) {
                echo '<option value="' . htmlspecialchars($player->id) . '">' . htmlspecialchars($player->username) . '</option>';
            }
            ?>
        </select></label>
        <br><br>
        <label>Black Player: <select name="blackId">
            <?php
            foreach ($players as $player) {
                echo '<option value="' . htmlspecialchars($player->id) . '">' . htmlspecialchars($player->username) . '</option>';
            }
            ?>
        </select></label>
        <br><br>
        <button type="submit">Start Game</button>
    </form>

</body>
</html>
```

4. **Validation**: ensure the selected players are **different** and exist in DB.

## Done when…

* Players in the form come from the `players` table (seeded list appears).
* `Database::get()` is the only place a PDO is created (static connection).
* Still no DB game records yet; board state is still hidden fields.

---

# Exercise 9-3 — Persist a game record + redirect with `gameId`

## Goal

Create a new row in `games` when players are chosen, then redirect to `play.php?game={id}`. Begin reading metadata (whose turn, move count) from DB instead of hidden fields. (Board state will move to DB in the next exercise.)

## Steps

1. **Create `GameRepository`** for the `games` table:

* Required columns: `white_player_id`, `black_player_id`, `status`, `result`, `current_turn`, `move_count`, timestamps. 

```php
<?php
namespace App\Infra;

use App\Database;
use PDO;

final class GameRepository {
    public function create(int $whiteId, int $blackId): int {
        $sql = "INSERT INTO games (white_player_id, black_player_id, status, result, current_turn, move_count)
                VALUES (:w, :b, 'active', 'ongoing', 'white', 0)";
        $st = Database::get()->prepare($sql);
        $st->execute([':w'=>$whiteId, ':b'=>$blackId]);
        return (int)Database::get()->lastInsertId();
    }

    public function getMeta(int $gameId): ?array {
        $sql = "SELECT id, white_player_id, black_player_id, current_turn, move_count, status
                FROM games WHERE id = :id";
        $st = Database::get()->prepare($sql);
        $st->execute([':id'=>$gameId]);
        return $st->fetch() ?: null;
    }
}
```

2. **Modify `start.php` (POST handler)**:

* On valid player choices: call `GameRepository::create()`.
* `header("Location: play.php?game=$id"); exit;`

3. **Modify `play.php`**:

* Require `?game=ID`; read meta via `GameRepository::getMeta()`.
* Display **“Now Playing: White (player1)”** or **“Black (player2)”** based on `current_turn`. You can resolve usernames with a simple join or an extra `PlayerRepository::findById()` call.
* Temporarily keep board/position in hidden inputs until Exercise 9-4—**but initialize them** when the game is first loaded.

4. **Validation** (still no sessions):

* When a move is attempted, confirm the move’s color matches `current_turn` from DB before accepting it.
* Display the prompt: **“Now Playing: White (username)”** (or Black) using players from `players` and turn from `games`. 

## Done when…

* Starting a game inserts into `games` and redirects with `?game=ID`.
* `play.php` reads the current turn from DB and shows the correct “Now Playing: …”.
* You validate that the piece being moved belongs to the current player’s color (use your engine’s piece-color logic).

---

# Exercise 9-4 — Store board state + moves in DB (with transactions)

## Goal

Move all ongoing state into the DB:

* Save the **current board** (serialized) in `games.current_board_state`, update `current_turn`, `move_count`.
* Append each move into `moves` with the correct fields (piece type, from/to squares, color, notation).
* Wrap write steps in a **transaction** so a move is all-or-nothing.
  Schema references: `games.current_board_state`, `games.move_count`, `games.current_turn`; `moves` table structure.

## Steps

1. **Update `GameRepository`**:

* Add `saveStateAfterMove()` method that updates `games.current_board_state`, `current_turn`, and `move_count`:

```php
public function saveStateAfterMove(int $gameId, string $boardSerialized, string $nextTurn, int $newMoveCount): void {
    $sql = "UPDATE games
            SET current_board_state = :b, current_turn = :t, move_count = :c, last_move_at = NOW()
            WHERE id = :id";
    $st = Database::get()->prepare($sql);
    $st->execute([':b'=>$boardSerialized, ':t'=>$nextTurn, ':c'=>$newMoveCount, ':id'=>$gameId]);
}
```

* Add `saveInitialGameState()` method that updates `games.current_board_state` for a given ID without affecting other fields (a trimmed-down version of the above).
* Add `current_board_state` to the SELECT clause in `getMeta()`.

2. **Create `MoveRepository`** that inserts into `moves`:

* Required fields: `game_id`, `move_number`, `player_color`, `piece_type`, `from_position`, `to_position`, plus flags as you can compute (captures, check) and notation when available.

```php
<?php
namespace App\Infra;
use App\Database;

final class MoveRepository {
    public function record(
        int $gameId, int $moveNumber, string $color, string $piece,
        string $from, string $to, ?string $algebraicNotation = null
    ): void {
        $sql = "INSERT INTO moves (game_id, move_number, player_color, piece_type, from_position, to_position, algebraic_notation)
                VALUES (:g, :n, :c, :p, :f, :t, :a)";
        $st = Database::get()->prepare($sql);
        if ($algebraicNotation === null) {
            $algebraicNotation = ($piece !== 'P' ? $piece : '') . $to;
        }
        $st->execute([
          ':g'=>$gameId, ':n'=>$moveNumber, ':c'=>$color, ':p'=>$piece,
          ':f'=>$from, ':t'=>$to, ':a'=>$algebraicNotation
        ]);
    }

    /** @return array[] */
    public function listForGame(int $gameId): array {
        $sql = "SELECT move_number, player_color, piece_type, from_position, to_position, algebraic_notation
                FROM moves WHERE game_id = :g ORDER BY id";
        $st = Database::get()->prepare($sql);
        $st->execute([':g'=>$gameId]);
        return $st->fetchAll();
    }
}
```

3. **Change `ChessBoard::movePiece()` method**:

* Validate **the moving piece color matches** `games.current_turn`. (the piece object has a `color` property)
* Begin transaction: `Database::get()->beginTransaction();` (you'll need to `use App\Database;` at the top of the file)
* Apply the move to the in-memory board (existing domain logic).
* Update `$this->gameMeta` values for `current_turn` and `move_count`.
* Serialize board to JSON and call `saveStateAfterMove()`.
* Insert a row in `moves` via `MoveRepository::record()`.
* Commit. On any exception, rollback and show a safe error message. (you'll need to add the try/catch block around the transaction code that catches PDOException)

4. **Render** the move list from DB (bottom of the board in `play.php`):

* Use `MoveRepository::listForGame()`; show “1. e4 e5 2. Nf3 Nc6 …” where you can (seed data example mirrors this).

5. **Initialization**:

* In `play.php`, rearrange the logic that loads the board state:

```php
$chessBoard = new ChessBoard($gameMeta);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start'], $_POST['end'])) {
    $chessBoard->movePiece($_POST['start'], $_POST['end']);
}
```

* Also in `play.php`, change all references to `$gameMeta` that appear below the above code to use `$chessBoard->gameMeta` instead (e.g., current turn, move count).
* Change the `ChessBoard` constructor to (you'll need to add `use App\Infra\GameRepository;` at the top of the file as well):

```php
public function __construct(public array $gameMeta) {
    if (!empty($gameMeta['current_board_state'])) {
        $this->board = unserialize($gameMeta['current_board_state']);
    } else {
        $this->setupBoard();
        $gameRepository = new GameRepository();
        $gameRepository->saveInitialGameState($gameMeta['id'], serialize($this->board));
    }
}
```

## Done when…

* Refreshing the page shows the **same position** (loaded from `games.current_board_state`).
* Each accepted move inserts a row in `moves` and updates `games` atomically.
* The **“Now Playing”** prompt flips color after each committed move.

---

# Exercise 9-5 — Create the game entry point (`index.php`)

## Goal

Create `index.php` that provides a button to start a new game and lists ongoing games.

## Steps

1. **Add HTML structure to `index.php`**:

* Basic HTML5 boilerplate with a centered `<h1>Chess Game</h1>` (can be copied from `play.php`).

2. **Add Start New Game to `index.php`**:

* Create a link or button that goes to `start.php` labeled something like "Start New Game".

3. **List ongoing games**:

* Below the "Start New Game" button, display a list of ongoing games with links to `play.php?game=ID` for each game.
* You'll likely need to add a new method in `GameRepository` to fetch ongoing games and use that in `index.php`:

```php
public function findOngoingGames(): array {
    $sql = "SELECT id, white_player_id, black_player_id, current_turn, move_count
            FROM games WHERE status = 'ongoing'";
    $st = Database::get()->prepare($sql);
    $st->execute();
    return $st->fetchAll();
}
```

4. **Add some styling** (optional):

* Use basic CSS to make the list and button look nice and centered.
* Ensure the overall layout is user-friendly.
* Consider using a CSS framework like Bootstrap for quicker styling.
* Feel free to dress up the `play.php` and `start.php` pages as well for consistency.

## Done when…

* `index.php` shows a "Start New Game" button and lists ongoing games with links to play them.
* Clicking "Start New Game" takes you to `start.php`, and selecting a game from the list takes you to `play.php?game=ID`.
* The overall application flow is smooth and intuitive.
