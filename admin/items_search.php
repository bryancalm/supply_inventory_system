<?php
session_start();
include '../database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    exit;
}

$q = $_GET['q'] ?? '';
$like = "%$q%";

$stmt = $conn->prepare(
    "SELECT items.*, categories.name AS cat_name
     FROM items
     JOIN categories ON items.category_id = categories.id
     WHERE items.name LIKE ?
     ORDER BY items.name ASC"
);
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()):
?>
<form method="POST" action="dashboard.php?page=items">
<tr>
    <td>
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input name="name" value="<?= $row['name'] ?>" class="form-control">
    </td>
    <td>
        <select name="category" class="form-control">
            <?php
            $cats = $conn->query("SELECT * FROM categories");
            while ($c = $cats->fetch_assoc()):
            ?>
                <option value="<?= $c['id'] ?>" <?= ($row['category_id']==$c['id'])?'selected':'' ?>>
                    <?= $c['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </td>
    <td>
        <input name="quantity" type="number" value="<?= $row['quantity'] ?>" class="form-control">
    </td>
    <td>
        <input name="unit" value="<?= $row['unit'] ?>" class="form-control">
    </td>
    <td>
        <input name="supplier" value="<?= $row['supplier'] ?>" class="form-control">
    </td>
    <td>
        <input name="price" type="number" step="0.01" value="<?= $row['price'] ?>" class="form-control">
    </td>
    <td class="text-center">
        <button name="update" class="btn btn-success btn-sm">Update</button>
        <a href="dashboard.php?page=items&delete=<?= $row['id'] ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Delete this item?')">
           Delete
        </a>
    </td>
</tr>
</form>
<?php endwhile; ?>
