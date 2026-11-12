<?php

class Member
{
    private PDO $conn;

    private const CATEGORIES = [
        'staff' => 'Staff',
        'undergraduate' => 'Undergraduate Student',
        'undergraduate student' => 'Undergraduate Student',
        'undergradute' => 'Undergraduate Student',
        'graduate student' => 'Graduate Student',
        'post graduate' => 'Graduate Student',
        'post graduate student' => 'Graduate Student',
        'research' => 'Research Student',
        'research student' => 'Research Student',
    ];

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function normalizeCategory(string $category): ?string
    {
        return self::CATEGORIES[strtolower(trim($category))] ?? null;
    }

    private function getFeeId(string $category): int
    {
        $stmt = $this->conn->prepare(
            'SELECT category_id FROM member_fee WHERE LOWER(TRIM(member_category)) = LOWER(TRIM(?))'
        );
        $stmt->execute([trim($category)]);
        $feeId = $stmt->fetchColumn();

        if ($feeId === false) {
            $normalized = $this->normalizeCategory($category);
            $feeCategoryMap = [
                'Staff' => 'Staff',
                'Research Student' => 'Research',
                'Graduate Student' => 'Post Graduate',
                'Undergraduate Student' => 'Undergraduate',
            ];
            if ($normalized !== null) {
                $stmt->execute([$feeCategoryMap[$normalized]]);
                $feeId = $stmt->fetchColumn();
            }
        }

        if ($feeId === false) {
            throw new InvalidArgumentException(
                'Please select a valid member category configured in member_fee.'
            );
        }

        return (int)$feeId;
    }

    private function getFeeCategories(): array
    {
        return $this->conn->query(
            'SELECT category_id, member_category, max_books, loan_days
             FROM member_fee
             ORDER BY category_id'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMember(
        string $name,
        string $email,
        string $phone,
        string $category
    ): int {
        $name = trim($name);
        $email = trim($email);
        $phone = trim($phone);

        if ($name === '') {
            throw new InvalidArgumentException('Member name is required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Enter a valid email address.');
        }
        $feeId = $this->getFeeId($category);
        $stmt = $this->conn->prepare(
            'INSERT INTO members (name, email, phone, active, category_id)
             VALUES (:name, :email, :phone, 1, :fee_id)'
        );
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':fee_id' => $feeId,
        ]);

        return (int)$this->conn->lastInsertId();
    }

    public function getById(int $memberId): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT m.member_id, m.name, m.email, m.phone,
                    mf.member_category AS category, mf.max_books, mf.loan_days,
                    m.active, m.category_id AS fee_id
             FROM members m
             LEFT JOIN member_fee mf ON mf.category_id = m.category_id
             WHERE m.member_id = ?'
        );
        $stmt->execute([$memberId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function showAll(): array
    {
        $sql = 'SELECT m.member_id, m.name, m.email, m.phone,
                       mf.member_category AS category, mf.max_books, mf.loan_days,
                       m.active, m.category_id AS fee_id,
                       COUNT(b.borrowing_id) AS total_books_borrowed,
                       SUM(CASE WHEN b.return_date IS NULL AND b.borrowing_id IS NOT NULL THEN 1 ELSE 0 END) AS currently_borrowed
                FROM members m
                LEFT JOIN member_fee mf ON mf.category_id = m.category_id
                LEFT JOIN borrowings b ON b.member_id = m.member_id
                GROUP BY m.member_id, m.name, m.email, m.phone,
                         mf.member_category, mf.max_books, mf.loan_days,
                         m.active, m.category_id
                ORDER BY m.member_id DESC';

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchMember(string $keyword): array
    {
        $like = '%' . trim($keyword) . '%';
        $stmt = $this->conn->prepare(
            'SELECT m.member_id, m.name, m.email, m.phone,
                    mf.member_category AS category, mf.max_books, mf.loan_days,
                    m.active, m.category_id AS fee_id,
                    COUNT(b.borrowing_id) AS total_books_borrowed,
                    SUM(CASE WHEN b.return_date IS NULL AND b.borrowing_id IS NOT NULL THEN 1 ELSE 0 END) AS currently_borrowed
             FROM members m
             LEFT JOIN member_fee mf ON mf.category_id = m.category_id
             LEFT JOIN borrowings b ON b.member_id = m.member_id
             WHERE m.name LIKE :name OR m.email LIKE :email OR m.phone LIKE :phone
             GROUP BY m.member_id, m.name, m.email, m.phone,
                      mf.member_category, mf.max_books, mf.loan_days,
                      m.active, m.category_id
             ORDER BY m.member_id DESC'
        );
        $stmt->execute([
            ':name' => $like,
            ':email' => $like,
            ':phone' => $like,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateMembers(
        int $memberId,
        string $name,
        string $email,
        string $phone,
        string $category
    ): bool {
        if (!$this->getById($memberId)) {
            throw new RuntimeException('Member not found.');
        }
        if (trim($name) === '') {
            throw new InvalidArgumentException('Member name is required.');
        }
        if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Enter a valid email address.');
        }
        $feeId = $this->getFeeId($category);
        $stmt = $this->conn->prepare(
            'UPDATE members SET name = :name, email = :email, phone = :phone,
                    category_id = :fee_id
             WHERE member_id = :id'
        );

        return $stmt->execute([
            ':name' => trim($name),
            ':email' => trim($email),
            ':phone' => trim($phone),
            ':fee_id' => $feeId,
            ':id' => $memberId,
        ]);
    }

    public function setStatus(int $memberId, bool $active): bool
    {
        if (!$this->getById($memberId)) {
            throw new RuntimeException('Member not found.');
        }

        $stmt = $this->conn->prepare('UPDATE members SET active = ? WHERE member_id = ?');
        return $stmt->execute([$active ? 1 : 0, $memberId]);
    }

    public function deleteMember(int $memberId): bool
    {
        if (!$this->getById($memberId)) {
            return false;
        }

        $check = $this->conn->prepare('SELECT COUNT(*) FROM borrowings WHERE member_id = ?');
        $check->execute([$memberId]);

        if ((int)$check->fetchColumn() > 0) {
            throw new RuntimeException(
                'Cannot delete this member because borrowing records exist. Set the member status to inactive instead.'
            );
        }

        $stmt = $this->conn->prepare('DELETE FROM members WHERE member_id = ?');
        $stmt->execute([$memberId]);

        return $stmt->rowCount() > 0;
    }

    public function showBorrowedBookCount(): array
    {
        $sql = 'SELECT m.member_id, m.name,
                       COUNT(b.borrowing_id) AS total_books_borrowed,
                       SUM(CASE WHEN b.return_date IS NULL AND b.borrowing_id IS NOT NULL THEN 1 ELSE 0 END) AS currently_borrowed
                FROM members m
                LEFT JOIN borrowings b ON b.member_id = m.member_id
                GROUP BY m.member_id, m.name
                ORDER BY m.member_id';
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function renderPage(?array $edit = null, ?string $keyword = null): void
    {
        $action = $_GET['action'] ?? 'list';
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        echo '<h2>Member Management</h2>';

        // Search members.
        echo '<h3>Search for a Member</h3>';
        echo '<form method="get" action="index.php">';
        echo '<input type="hidden" name="page" value="members">';
        echo '<label for="member_keyword">Name, email or phone:</label> ';
        echo '<input type="search" id="member_keyword" name="keyword" value="' .
            self::h($keyword ?? '') . '" placeholder="Search member">';
        echo '<button type="submit">Search</button> ';
        echo '<a href="index.php?page=members">Clear</a>';
        echo '</form>';

        // View one member.
        if ($action === 'view' && $id > 0) {
            $member = $this->getMemberDetails($id);
            echo '<h3>Member Details</h3>';

            if (!$member) {
                echo '<p>Member not found.</p>';
            } else {
                echo '<table border="1" cellpadding="6">';
                $labels = [
                    'member_id' => 'Member ID',
                    'name' => 'Name',
                    'email' => 'Email',
                    'phone' => 'Phone',
                    'category' => 'Category',
                    'max_books' => 'Maximum Books',
                    'loan_days' => 'Loan Days',
                    'active' => 'Status',
                    'fee_id' => 'Fee ID',
                    'total_books_borrowed' => 'Total Books Borrowed',
                    'currently_borrowed' => 'Currently Borrowed',
                ];
                foreach ($labels as $key => $label) {
                    $value = $member[$key] ?? '';
                    if ($key === 'active') {
                        $value = (int)$value === 1 ? 'Active' : 'Inactive';
                    }
                    echo '<tr><th>' . self::h($label) . '</th><td>' . self::h($value) . '</td></tr>';
                }
                echo '</table>';
                echo '<p><a href="index.php?page=members&action=edit&id=' .
                    self::h($id) . '">Edit Member</a> | ';
                echo '<a href="index.php?page=members">Back to Members</a></p>';
            }
        }

        // Add or update form.
        $isEdit = is_array($edit);
        $member = $isEdit ? $edit : [];
        $categories = $this->getFeeCategories();

        echo '<h3>' . ($isEdit ? 'Update Member' : 'Add a Member') . '</h3>';
        echo '<form method="post" action="index.php?page=members">';
        echo '<input type="hidden" name="action" value="save">';
        echo '<input type="hidden" name="id" value="' . self::h($member['member_id'] ?? '') . '">';

        self::input('Name', 'name', $member['name'] ?? '', 'text', true);
        self::input('Email', 'email', $member['email'] ?? '', 'email', true);
        self::input('Phone', 'phone', $member['phone'] ?? '', 'text');

        echo '<p><label for="category">Category:</label><br>';
        echo '<select name="category" id="category" required>';
        echo '<option value="">Select Category</option>';
        foreach ($categories as $category) {
            $selected = (($member['category'] ?? '') === $category['member_category']) ? ' selected' : '';
            $label = $category['member_category'] . ' (Max books: ' .
                $category['max_books'] . ', Loan days: ' . $category['loan_days'] . ')';
            echo '<option value="' . self::h($category['member_category']) . '"' . $selected . '>' .
                self::h($label) . '</option>';
        }
        echo '</select></p>';

        echo '<button type="submit">' . ($isEdit ? 'Update Member' : 'Add Member') . '</button> ';
        echo '<a href="index.php?page=members">Cancel</a>';
        echo '</form>';

        // Set active/inactive status for an edited or viewed member.
        if ($isEdit) {
            echo '<h3>Set Member Status</h3>';
            echo '<form method="post" action="index.php?page=members">';
            echo '<input type="hidden" name="action" value="status">';
            echo '<input type="hidden" name="id" value="' . self::h($member['member_id']) . '">';
            echo '<select name="active">';
            echo '<option value="1"' . ((int)$member['active'] === 1 ? ' selected' : '') . '>Active</option>';
            echo '<option value="0"' . ((int)$member['active'] === 0 ? ' selected' : '') . '>Inactive</option>';
            echo '</select> ';
            echo '<button type="submit">Update Status</button>';
            echo '</form>';
        }

        // All members or search results.
        $members = ($keyword !== null && trim($keyword) !== '')
            ? $this->searchMember(trim($keyword))
            : $this->showAll();

        echo '<h3>' . (($keyword !== null && trim($keyword) !== '') ? 'Search Results' : 'Display All Members') . '</h3>';
        $this->renderMemberTable($members);

    }

    private function getMemberDetails(int $memberId): ?array
    {
        $sql = 'SELECT m.member_id, m.name, m.email, m.phone,
                       mf.member_category AS category, mf.max_books, mf.loan_days,
                       m.active, m.category_id AS fee_id,
                       COUNT(b.borrowing_id) AS total_books_borrowed,
                       SUM(CASE WHEN b.return_date IS NULL AND b.borrowing_id IS NOT NULL THEN 1 ELSE 0 END) AS currently_borrowed
                FROM members m
                LEFT JOIN member_fee mf ON mf.category_id = m.category_id
                LEFT JOIN borrowings b ON b.member_id = m.member_id
                WHERE m.member_id = ?
                GROUP BY m.member_id, m.name, m.email, m.phone,
                         mf.member_category, mf.max_books, mf.loan_days,
                         m.active, m.category_id';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$memberId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function renderMemberTable(array $members): void
    {
        if (!$members) {
            echo '<p>No members found.</p>';
            return;
        }

        echo '<table border="1" cellpadding="6">';
        echo '<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Category</th>';
        echo '<th>Status</th><th>Total Borrowed</th><th>Currently Borrowed</th><th>Actions</th></tr>';

        foreach ($members as $member) {
            $id = (int)$member['member_id'];
            echo '<tr>';
            echo '<td>' . self::h($id) . '</td>';
            echo '<td>' . self::h($member['name']) . '</td>';
            echo '<td>' . self::h($member['email']) . '</td>';
            echo '<td>' . self::h($member['phone']) . '</td>';
            echo '<td>' . self::h($member['category']) . '</td>';
            echo '<td>' . ((int)$member['active'] === 1 ? 'Active' : 'Inactive') . '</td>';
            echo '<td>' . self::h($member['total_books_borrowed'] ?? 0) . '</td>';
            echo '<td>' . self::h($member['currently_borrowed'] ?? 0) . '</td>';
            echo '<td>';
            echo '<a href="index.php?page=members&action=view&id=' . self::h($id) . '">View</a> | ';
            echo '<a href="index.php?page=members&action=edit&id=' . self::h($id) . '">Edit</a>';
            echo '<form method="post" action="index.php?page=members" style="display:inline" onsubmit="return confirm(\'Delete this member?\')">';
            echo '<input type="hidden" name="action" value="delete">';
            echo '<input type="hidden" name="id" value="' . self::h($id) . '">';
            echo ' <button type="submit">Delete</button>';
            echo '</form>';
            echo '<form method="post" action="index.php?page=members" style="display:inline">';
            echo '<input type="hidden" name="action" value="status">';
            echo '<input type="hidden" name="id" value="' . self::h($id) . '">';
            $nextStatus = (int)$member['active'] === 1 ? 0 : 1;
            echo '<input type="hidden" name="active" value="' . $nextStatus . '">';
            echo ' <button type="submit">' . ($nextStatus === 1 ? 'Activate' : 'Deactivate') . '</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
    }

    private static function input(
        string $label,
        string $name,
        mixed $value = '',
        string $type = 'text',
        bool $required = false,
        string $extra = ''
    ): void {
        echo '<p><label for="' . self::h($name) . '">' . self::h($label) . ':</label><br>';
        echo '<input type="' . self::h($type) . '" id="' . self::h($name) .
            '" name="' . self::h($name) . '" value="' . self::h($value) . '"' .
            ($required ? ' required' : '') . ' ' . $extra . '>';
        echo '</p>';
    }

    private static function h(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>
