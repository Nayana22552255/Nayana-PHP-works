<?php
require_once "Database.php";

class Author
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // ADD AUTHOR
    public function addAuthor(string $name, ?string $biography): void
    {
        $sql = "INSERT INTO authors(author_name, biography)
                VALUES(:name, :biography)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':biography' => $biography
        ]);
    }

    // DISPLAY ALL AUTHORS
    public function showAll(): array
    {
        $sql = "SELECT * FROM authors ORDER BY author_id";

        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // SEARCH AUTHOR
    public function search(string $keyword): array
    {
        $sql = "SELECT * FROM authors
                WHERE author_name LIKE :keyword
                ORDER BY author_name";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':keyword' => '%' . $keyword . '%'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // VIEW SINGLE AUTHOR
    public function getAuthor(int $id): array|false
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM authors WHERE author_id = :id"
        );

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE AUTHOR
    public function update(
        int $id,
        string $name,
        ?string $biography
    ): void {

        $sql = "UPDATE authors
                SET author_name = :name,
                    biography = :biography
                WHERE author_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':biography' => $biography,
            ':id' => $id
        ]);
    }

    // DELETE AUTHOR
    public function delete(int $id): bool
    {
        $check = $this->conn->prepare(
            "SELECT COUNT(*) FROM book_authors
             WHERE author_id = :id"
        );

        $check->execute([':id' => $id]);

        if ((int)$check->fetchColumn() > 0) {
            return false;
        }

        $stmt = $this->conn->prepare(
            "DELETE FROM authors WHERE author_id = :id"
        );

        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }
}


/*
$author = new Author($conn);

$message = "";
$page = $_GET['page'] ?? 'home';

function clean(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}


// HANDLE FORM SUBMISSIONS

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD
    if (isset($_POST['add'])) {

        $author->addAuthor(
            trim($_POST['author_name']),
            trim($_POST['biography']) ?: null
        );

        header("Location: Author.php?message=added");
        exit;
    }

    // UPDATE
    if (isset($_POST['update'])) {

        $author->update(
            (int)$_POST['author_id'],
            trim($_POST['author_name']),
            trim($_POST['biography']) ?: null
        );

        header("Location: Author.php?message=updated");
        exit;
    }

    // DELETE
    if (isset($_POST['delete'])) {

        $deleted = $author->delete((int)$_POST['author_id']);

        $status = $deleted ? 'deleted' : 'delete_failed';

        header("Location: Author.php?message=$status");
        exit;
    }
}


// DISPLAY SUCCESS / ERROR MESSAGE

if (isset($_GET['message'])) {

    $messages = [
        'added' => 'Author added successfully!',
        'updated' => 'Author updated successfully!',
        'deleted' => 'Author deleted successfully!',
        'delete_failed' => 'Author cannot be deleted. The author may be associated with books.'
    ];

    $message = $messages[$_GET['message']] ?? '';
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Author Management</title>
</head>

<body>

<h1>AUTHOR MANAGEMENT SYSTEM</h1>

<hr>

<?php if ($message): ?>
    <p><b><?= clean($message) ?></b></p>
<?php endif; ?>


<!-- MAIN MENU -->

<p>
    <a href="Author.php">Home</a> |
    <a href="Author.php?page=add">Add Author</a> |
    <a href="Author.php?page=display">Display Authors</a> |
    <a href="Author.php?page=search">Search Author</a>
</p>

<hr>


<?php

// HOME PAGE

if ($page === 'home') {

    echo "<h2>Welcome to Author Management</h2>";
    echo "<p>Select an operation from the above menu.</p>";

}


// ADD AUTHOR PAGE

elseif ($page === 'add') {

?>

    <h2>Add New Author</h2>

    <form method="POST" action="Author.php?page=add">

        <label>Author Name:</label><br>
        <input type="text" name="author_name" required>
        <br><br>

        <label>Biography:</label><br>
        <textarea name="biography" rows="5" cols="40"></textarea>
        <br><br>

        <button type="submit" name="add">Add Author</button>

    </form>

<?php
}


// DISPLAY ALL AUTHORS

elseif ($page === 'display') {

    $authors = $author->showAll();

?>

    <h2>All Authors</h2>

    <?php if (empty($authors)): ?>

        <p>No authors found.</p>

    <?php else: ?>

        <table border="1" cellpadding="8">

            <tr>
                <th>Author ID</th>
                <th>Author Name</th>
                <th>Biography</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($authors as $row): ?>

                <tr>

                    <td><?= clean($row['author_id']) ?></td>

                    <td><?= clean($row['author_name']) ?></td>

                    <td><?= clean($row['biography'] ?? 'Not available') ?></td>

                    <td>

                        <a href="Author.php?page=view&id=<?= clean($row['author_id']) ?>">
                            View
                        </a>

                        |

                        <a href="Author.php?page=edit&id=<?= clean($row['author_id']) ?>">
                            Edit
                        </a>

                        |

                        <form method="POST"
                              action="Author.php?page=display"
                              style="display:inline;">

                            <input type="hidden"
                                   name="author_id"
                                   value="<?= clean($row['author_id']) ?>">

                            <button type="submit"
                                    name="delete"
                                    onclick="return confirm('Delete this author?')">

                                Delete

                            </button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    <?php endif; ?>

<?php
}


// SEARCH AUTHOR PAGE

elseif ($page === 'search') {

    $results = [];

    if (isset($_GET['keyword'])) {
        $results = $author->search(trim($_GET['keyword']));
    }

?>

    <h2>Search Author</h2>

    <form method="GET" action="Author.php">

        <input type="hidden" name="page" value="search">

        <label>Enter Author Name:</label>

        <input type="text"
               name="keyword"
               value="<?= clean($_GET['keyword'] ?? '') ?>"
               required>

        <button type="submit">Search</button>

    </form>

    <br>

    <?php if (isset($_GET['keyword'])): ?>

        <?php if (empty($results)): ?>

            <p>No matching authors found.</p>

        <?php else: ?>

            <h3>Search Results</h3>

            <table border="1" cellpadding="8">

                <tr>
                    <th>ID</th>
                    <th>Author Name</th>
                    <th>Action</th>
                </tr>

                <?php foreach ($results as $row): ?>

                    <tr>

                        <td><?= clean($row['author_id']) ?></td>

                        <td><?= clean($row['author_name']) ?></td>

                        <td>

                            <a href="Author.php?page=view&id=<?= clean($row['author_id']) ?>">
                                View Details
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        <?php endif; ?>

    <?php endif; ?>

<?php
}


// VIEW AUTHOR DETAILS

elseif ($page === 'view') {

    $id = (int)($_GET['id'] ?? 0);

    $row = $author->getAuthor($id);

?>

    <h2>Author Details</h2>

    <?php if ($row): ?>

        <p><b>Author ID:</b> <?= clean($row['author_id']) ?></p>

        <p><b>Author Name:</b> <?= clean($row['author_name']) ?></p>

        <p><b>Biography:</b> <?= clean($row['biography'] ?? 'Not available') ?></p>

        <a href="Author.php?page=edit&id=<?= clean($row['author_id']) ?>">
            Edit Author
        </a>

        <br><br>

        <a href="Author.php?page=display">Back to Authors</a>

    <?php else: ?>

        <p>Author not found.</p>

    <?php endif; ?>

<?php
}


// EDIT AUTHOR PAGE

elseif ($page === 'edit') {

    $id = (int)($_GET['id'] ?? 0);

    $row = $author->getAuthor($id);

?>

    <h2>Edit Author</h2>

    <?php if ($row): ?>

        <form method="POST" action="Author.php?page=edit">

            <input type="hidden"
                   name="author_id"
                   value="<?= clean($row['author_id']) ?>">

            <label>Author Name:</label><br>

            <input type="text"
                   name="author_name"
                   value="<?= clean($row['author_name']) ?>"
                   required>

            <br><br>

            <label>Biography:</label><br>

            <textarea name="biography"
                      rows="5"
                      cols="40"><?= clean($row['biography']) ?></textarea>

            <br><br>

            <button type="submit" name="update">
                Update Author
            </button>

        </form>

    <?php else: ?>

        <p>Author not found.</p>

    <?php endif; ?>

<?php
}

?>

<hr>

<p>Author Management System</p>

</body>
</html>*/