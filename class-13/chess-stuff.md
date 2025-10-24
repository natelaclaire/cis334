## Steps

1. **Game completion**

* Add a small domain method to detect checkmate/stalemate (or a temporary “End Game” button).
* When a game ends, update `games.status` (`completed` or `draw`) and `games.result` (`white_wins`, `black_wins`, or `draw`). 
* (Optional) Set `completed_at` timestamp.

2. **Strict move validation**

* Ensure the **piece being moved belongs to the current color** (already in place).
* Forbid moving the opponent’s pieces, illegal moves, or moving when it’s not your turn (use engine + `games.current_turn`).
* If violated, **reject** and re-render with an error message; **do not** write to DB.

3. **Now Playing banner**

* Resolve usernames for both players and print:
  `Now Playing: White (player1)` or `Now Playing: Black (player2)` from `players.username`.  
* A small helper in `GameRepository` can join `games` to `players` (white/black) to return both names.

4. **Lightweight routing & PRG**

* Keep entry points minimal:

  * `index.php` – home
  * `start.php` – choose players, `POST` → create DB game → `Location: play.php?game=ID`
  * `play.php` – GET: show board; POST: process move

5. **Data hygiene**

* All DB writes via **prepared statements**.
* Cast/validate all query and post params (`(int)$_GET['game']`, validate squares with `/^[a-h][1-8]$/`).
* Escape all HTML output with `htmlspecialchars`.

6. **(Optional) Stats**

* When a game completes, update players’ stats (total, wins/losses/draws). Your schema even includes a stored proc example for stats updates and a `player_stats` view if you want to explore later. 

## Done when…

* You can start a game from real players, play legal moves with correct turn and ownership validation, see the move list, refresh safely, and end a game with a result set in `games`.

---

## Minimal snippet index

**Displaying the “Now Playing” banner**

```php
// In play.php after loading meta:
$meta = $gameRepo->getMeta($gameId); // ['white_player_id'=>..., 'black_player_id'=>..., 'current_turn'=>'white']
$white = $playerRepo->findById($meta['white_player_id'])->username;
$black = $playerRepo->findById($meta['black_player_id'])->username;
$banner = $meta['current_turn'] === 'white'
  ? "Now Playing: White ($white)"
  : "Now Playing: Black ($black)";
echo "<p>" . htmlspecialchars($banner, ENT_QUOTES) . "</p>";
```

**Guarding a move**

```php
// $move = e.g., ['from'=>'e2','to'=>'e4']; $color = $meta['current_turn'];
if (!$engine->isLegalMove($board, $move)) { /* show error; no DB writes */ }
if (!$engine->pieceAt($move['from'])->belongsTo($color)) { /* show error; no DB writes */ }
```

**Transaction around a move**

```php
$pdo = \App\Database::get();
try {
  $pdo->beginTransaction();

  $moveNo = $meta['move_count'] + ($meta['current_turn'] === 'black' ? 0 : 1);

  $moveRepo->record($gameId, $moveNo, $meta['current_turn'], $pieceType, $move['from'], $move['to']);
  $engine->apply($board, $move);
  $nextTurn = $meta['current_turn'] === 'white' ? 'black' : 'white';
  $gameRepo->saveStateAfterMove($gameId, json_encode($board), $nextTurn, $meta['move_count'] + 1);

  $pdo->commit();
} catch (\Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  // show a safe error message
}
```

---

## Instructor notes

* We deliberately **avoid sessions** (per your constraint) and rely on query strings + hidden fields until Exercise 4 migrates state into DB.
* The schema’s `moves` structure and the `games` fields (`current_turn`, `move_count`, `current_board_state`) are designed exactly for this workflow. 
* Seed data lets students see instant content (`player1`, `player2`, a sample game with a few moves). 
* The domain engine can remain mostly unchanged; we’re just adding persistence and structure around it.
* The progression emphasizes **incremental DB integration**—first reading players, then creating games, then persisting state and moves—so students can focus on one piece at a time.
* The final exercise polishes the experience with validation, completion, and a lightweight routing pattern (PRG).
* Encourage students to test each step thoroughly before moving on, especially the transaction logic in Exercise 4.
* Feel free to adapt the complexity based on your students’ familiarity with PDO and database concepts.
* This progression can be extended further with features like player stats, game history, or even a simple AI opponent if desired.
* Happy coding!
