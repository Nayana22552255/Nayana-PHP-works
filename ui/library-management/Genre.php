<?php

class Genre
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Return one genre, including its parent genre name.
    public function getById(int $genreId): ?array
    {
        $sql = "SELECT g.genre_id, g.genre_name, g.parent_genre_id,
                       p.genre_name AS parent_name
                FROM genres g
                LEFT JOIN genres p ON p.genre_id = g.parent_genre_id
                WHERE g.genre_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$genreId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    // Return all genres.
    public function getAll(): array
    {
        $sql = "SELECT g.genre_id, g.genre_name, g.parent_genre_id,
                       p.genre_name AS parent_name
                FROM genres g
                LEFT JOIN genres p ON p.genre_id = g.parent_genre_id
                ORDER BY g.genre_name";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Search genres by name.
    public function searchGenres(string $keyword): array
    {
        $sql = "SELECT g.genre_id, g.genre_name, g.parent_genre_id,
                       p.genre_name AS parent_name
                FROM genres g
                LEFT JOIN genres p ON p.genre_id = g.parent_genre_id
                WHERE g.genre_name LIKE ?
                ORDER BY g.genre_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['%' . $keyword . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a genre.
    public function add(string $name, ?int $parentId = null): void
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('Genre name is required.');
        }

        $this->validateParent(null, $parentId);

        $stmt = $this->conn->prepare(
            "INSERT INTO genres (genre_name, parent_genre_id)
             VALUES (:name, :parent_id)"
        );
        $stmt->execute([
            ':name' => $name,
            ':parent_id' => $parentId
        ]);
    }

    // Update a genre.
    public function update(int $genreId, string $name, ?int $parentId = null): void
    {
        $name = trim($name);

        if ($genreId < 1 || $name === '') {
            throw new InvalidArgumentException('A valid genre ID and name are required.');
        }

        if (!$this->getById($genreId)) {
            throw new RuntimeException('Genre not found.');
        }

        $this->validateParent($genreId, $parentId);

        // Prevent making one of this genre's descendants its parent.
        if ($parentId !== null) {
            $current = $parentId;
            while ($current !== null) {
                if ($current === $genreId) {
                    throw new InvalidArgumentException(
                        'A genre cannot be its own parent or a descendant of itself.'
                    );
                }

                $parent = $this->getById($current);
                $current = $parent && $parent['parent_genre_id'] !== null
                    ? (int)$parent['parent_genre_id']
                    : null;
            }
        }

        $stmt = $this->conn->prepare(
            "UPDATE genres
             SET genre_name = :name, parent_genre_id = :parent_id
             WHERE genre_id = :id"
        );
        $stmt->execute([
            ':name' => $name,
            ':parent_id' => $parentId,
            ':id' => $genreId
        ]);
    }

    // Delete a genre only when it is not used by books or child genres.
    public function delete(int $genreId): bool
    {
        if (!$this->getById($genreId)) {
            throw new RuntimeException('Genre not found.');
        }

        $checkBooks = $this->conn->prepare(
            "SELECT COUNT(*) FROM books WHERE genre_id = ?"
        );
        $checkBooks->execute([$genreId]);

        if ((int)$checkBooks->fetchColumn() > 0) {
            throw new RuntimeException(
                'Cannot delete this genre because it is assigned to books.'
            );
        }

        $checkChildren = $this->conn->prepare(
            "SELECT COUNT(*) FROM genres WHERE parent_genre_id = ?"
        );
        $checkChildren->execute([$genreId]);

        if ((int)$checkChildren->fetchColumn() > 0) {
            throw new RuntimeException(
                'Cannot delete this genre because other genres use it as their parent.'
            );
        }

        $stmt = $this->conn->prepare("DELETE FROM genres WHERE genre_id = ?");
        $stmt->execute([$genreId]);

        return $stmt->rowCount() > 0;
    }

    private function validateParent(?int $genreId, ?int $parentId): void
    {
        if ($parentId === null) {
            return;
        }

        if ($parentId < 1 || $parentId === $genreId) {
            throw new InvalidArgumentException('Please select a valid parent genre.');
        }

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM genres WHERE genre_id = ?"
        );
        $stmt->execute([$parentId]);

        if ((int)$stmt->fetchColumn() === 0) {
            throw new InvalidArgumentException('Selected parent genre does not exist.');
        }
    }

    // Render the complete HTML-based Genre Management page.
    // index.php can call: (new Genre($conn))->renderPage($edit, $_GET['keyword'] ?? null);
    public function renderPage(?array $edit = null, ?string $keyword = null): void
    {
        $action = $_GET['action'] ?? 'list';
        $viewId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        echo '<h2>Genre Management</h2>';

        // Search form.
        echo '<form method="get" action="index.php">';
        echo '<input type="hidden" name="page" value="genres">';
        echo '<label for="genre_keyword">Search genre:</label> ';
        echo '<input type="search" id="genre_keyword" name="keyword" value="' .
            self::h($keyword ?? '') . '" placeholder="Enter genre name">';
        echo '<button type="submit">Search</button> ';
        echo '<a href="index.php?page=genres">Clear</a>';
        echo '</form>';

        // View one genre.
        if ($action === 'view' && $viewId > 0) {
            $genre = $this->getById($viewId);

            echo '<h3>Genre Details</h3>';

            if (!$genre) {
                echo '<p>Genre not found.</p>';
            } else {
                echo '<table border="1" cellpadding="6">';
                echo '<tr><th>Genre ID</th><td>' . self::h($genre['genre_id']) . '</td></tr>';
                echo '<tr><th>Genre Name</th><td>' . self::h($genre['genre_name']) . '</td></tr>';
                echo '<tr><th>Parent Genre</th><td>' .
                    self::h($genre['parent_name'] ?? 'None') . '</td></tr>';
                echo '</table>';
                echo '<p><a href="index.php?page=genres&action=edit&id=' .
                    self::h($genre['genre_id']) . '">Edit</a> | ';
                echo '<a href="index.php?page=genres">Back to Genres</a></p>';
            }

            $this->renderGenreList($keyword);
            return;
        }

        // Add or edit form.
        $isEdit = is_array($edit);
        $currentId = $isEdit ? (int)$edit['genre_id'] : 0;

        echo '<h3>' . ($isEdit ? 'Edit Genre' : 'Add New Genre') . '</h3>';
        echo '<form method="post" action="index.php?page=genres">';
        echo '<input type="hidden" name="action" value="save">';
        echo '<input type="hidden" name="id" value="' . self::h($currentId) . '">';

        echo '<p><label for="genre_name">Genre Name:</label><br>';
        echo '<input type="text" id="genre_name" name="genre_name" maxlength="20" required value="' .
            self::h($isEdit ? ($edit['genre_name'] ?? '') : '') . '"></p>';

        echo '<p><label for="parent_genre_id">Parent Genre:</label><br>';
        echo '<select id="parent_genre_id" name="parent_genre_id">';
        echo '<option value="">None (Main Genre)</option>';

        foreach ($this->getAll() as $parent) {
            $parentId = (int)$parent['genre_id'];

            // Do not allow a genre to be selected as its own parent.
            if ($isEdit && $parentId === $currentId) {
                continue;
            }

            $selectedParent = $isEdit && $edit['parent_genre_id'] !== null
                ? (int)$edit['parent_genre_id']
                : 0;

            $selected = $selectedParent === $parentId ? ' selected' : '';

            echo '<option value="' . self::h($parentId) . '"' . $selected . '>' .
                self::h($parent['genre_name']) . '</option>';
        }

        echo '</select></p>';
        echo '<button type="submit">' . ($isEdit ? 'Update Genre' : 'Add Genre') . '</button> ';
        echo '<a href="index.php?page=genres">Cancel</a>';
        echo '</form>';

        // Display all or searched genres.
        $this->renderGenreList($keyword);
    }

    private function renderGenreList(?string $keyword): void
    {
        $genres = ($keyword !== null && trim($keyword) !== '')
            ? $this->searchGenres(trim($keyword))
            : $this->getAll();

        echo '<h3>' . (($keyword !== null && trim($keyword) !== '')
            ? 'Search Results'
            : 'All Genres') . '</h3>';

        if (!$genres) {
            echo '<p>No genres found.</p>';
            return;
        }

        echo '<table border="1" cellpadding="6">';
        echo '<tr><th>ID</th><th>Genre Name</th><th>Parent Genre</th><th>Actions</th></tr>';

        foreach ($genres as $genre) {
            $id = (int)$genre['genre_id'];

            echo '<tr>';
            echo '<td>' . self::h($id) . '</td>';
            echo '<td>' . self::h($genre['genre_name']) . '</td>';
            echo '<td>' . self::h($genre['parent_name'] ?? 'None') . '</td>';
            echo '<td>';
            echo '<a href="index.php?page=genres&action=edit&id=' . self::h($id) . '">Edit</a>';

            echo '<form method="post" action="index.php?page=genres" style="display:inline" ';
            echo 'onsubmit="return confirm(\'Are you sure you want to delete this genre?\')">';
            echo '<input type="hidden" name="action" value="delete">';
            echo '<input type="hidden" name="id" value="' . self::h($id) . '">';
            echo ' <button type="submit">Delete</button>';
            echo '</form>';

            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
    }

    private static function h(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>
