<?php
require_once "config.php";

if (!User::isLoggedIn() || !User::isAdmin()) {
    header("Location: login.php");
    exit;
}

$pdo = Database::getConnection();
$error = "";
$success = "";

function uploadProductImage(string $fieldName, string &$error): string
{
    if (!isset($_FILES[$fieldName]) || ($_FILES[$fieldName]["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return "";
    }

    if ($_FILES[$fieldName]["error"] !== UPLOAD_ERR_OK) {
        $error = "Upload failed (errorcode: " . (int)$_FILES[$fieldName]["error"] . ").";
        return "";
    }

    $ext = strtolower(pathinfo((string)$_FILES[$fieldName]["name"], PATHINFO_EXTENSION));
    $allowed = ["jpg", "jpeg", "png", "webp"];
    if (!in_array($ext, $allowed, true)) {
        $error = "Invalid file type. Please use jpg, jpeg, png, or webp.";
        return "";
    }

    $uploadDirAbs = __DIR__ . "/public/uploads/products";
    if (!is_dir($uploadDirAbs)) {
        @mkdir($uploadDirAbs, 0755, true);
    }
    if (!is_dir($uploadDirAbs) || !is_writable($uploadDirAbs)) {
        $error = "Upload folder is not writable: public/uploads/products";
        return "";
    }

    $filename = "p_" . time() . "_" . bin2hex(random_bytes(6)) . "." . $ext;
    $targetAbs = $uploadDirAbs . "/" . $filename;

    if (!move_uploaded_file((string)$_FILES[$fieldName]["tmp_name"], $targetAbs)) {
        $error = "Could not save the file.";
        return "";
    }

    return "../public/uploads/products/" . $filename;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "add") {
        $title       = trim($_POST["title"] ?? "");
        $price       = (float)($_POST["price"] ?? 0);
        $category    = trim($_POST["category"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $image = uploadProductImage("image_file", $error);

        if ($title === "" || $price <= 0) {
            $error = "Title and price are required.";
        } elseif ($error === "") {
            $stmt = $pdo->prepare("
                INSERT INTO products (title, price, category, image, description)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $price, $category, $image, $description]);
            $success = "Product added.";
        }

    } 
    elseif ($action === "update") {
        $id          = (int)($_POST["id"] ?? 0);
        $title       = trim($_POST["title"] ?? "");
        $price       = (float)($_POST["price"] ?? 0);
        $category    = trim($_POST["category"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $currentImage = (string)($_POST["current_image"] ?? "");

        $newImage = uploadProductImage("image_file", $error);
        $imageToSave = $newImage !== "" ? $newImage : $currentImage;

        if ($id <= 0 || $title === "" || $price <= 0) {
            $error = "Invalid product data.";
        } elseif ($error === "") {
            $stmt = $pdo->prepare("
                UPDATE products
                SET title = ?, price = ?, category = ?, image = ?, description = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $price, $category, $imageToSave, $description, $id]);
            $success = "Product updated.";
        }

    } 
    elseif ($action === "delete") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $success = "delete product.";
        } else {
            $error = "Invalid product.";
        }
    }
}

$editProduct = null;
if (isset($_GET["edit"])) {
    $editId = (int)$_GET["edit"];
    if ($editId > 0) {
        $stmt = $pdo->prepare("
            SELECT id, title, price, category, description, image
            FROM products
            WHERE id = ?
        ");
        $stmt->execute([$editId]);
        $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$stmt = $pdo->query("
    SELECT id, title, price, category
    FROM products
    ORDER BY id ASC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user = User::currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="Assets/Images/logo_chairway.png" type="icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CHAIRWAY/Admin/Manage products</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">CHAIRWAY</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNavbar" aria-controls="mainNavbar"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="pages/Artikelen.php">Products</a></li>
        <li class="nav-item"><a class="nav-link active" href="admin_products.php">Admin products</a></li>
      </ul>
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item d-flex align-items-center me-2">
            <span class="navbar-text small">
                <?= htmlspecialchars((string)("Hello, " . $user["name"] ?? "")) ?>
            </span>
        </li>
        <li class="nav-item"><a class="nav-link" href="handlers/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<main>
<div class="container my-4">
    <h1 class="mb-4">Manage products</h1>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Add new product</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="row g-2">
                    <div class="col-12 col-md-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Price (€)</label>
                        <input type="number" name="price" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control">
                    </div>
                    <div class="col-md-4 mx-auto">
                        <label class="form-label" for="f">Photo (optional)</label>
                        <input id="f" type="file" name="image_file" class="d-none" accept="image/*">

                        <div class="input-group">
                        <label class="btn btn-outline-secondary" for="f">Choose file</label>
                        <input id="fn" class="form-control" value="No file chosen" readonly>
                        </div>

                        <script>
                        f.onchange = () => fn.value = f.files[0]?.name || "No file chosen";
                        </script>
                    </div>
                </div>
                <button type="submit" class="btn btn-dark mt-3">Add product</button>
            </form>
        </div>
    </div>

    <h2 class="h4 mb-3">Existing products</h2>
    <div class="table-responsive">
    <table class="table table-striped align-middle table-sm">
        <thead>
        <tr>
            <th class="d-none d-md-table-cell">ID</th>
            <th>Title</th>
            <th class="d-none d-sm-table-cell">Category</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr class="border-top">
                <td class="d-none d-md-table-cell"><?= (int)$p["id"] ?></td>
                <td><?= htmlspecialchars((string)$p["title"]) ?></td>
                <td class="d-none d-sm-table-cell"><?= htmlspecialchars((string)$p["category"]) ?></td>
                <td class="fw-semibold">€ <?= number_format((float)$p["price"], 2, ",", ".") ?></td>
                <td class="d-flex flex-column flex-md-row gap-2">
                    <a href="admin_products.php?edit=<?= (int)$p["id"] ?>"
                    class="btn btn-sm btn-outline-primary w-100 w-md-auto">
                        Edit
                    </a>

                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this?');" class="w-100 w-md-auto">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$p["id"] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 w-md-auto">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <?php if ($editProduct): ?>
        <div class="card mt-4">
            <div class="card-header">
                Edit product #<?= (int)$editProduct["id"] ?>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= (int)$editProduct["id"] ?>">
                    <input type="hidden" name="current_image" value="<?= htmlspecialchars((string)($editProduct["image"] ?? "")) ?>">

                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control"
                                   value="<?= htmlspecialchars((string)$editProduct["title"]) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Price (€)</label>
                            <input type="number" name="price" class="form-control" min="0" step="0.01"
                                   value="<?= htmlspecialchars((string)$editProduct["price"]) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control"
                                   value="<?= htmlspecialchars((string)$editProduct["category"]) ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control"
                                   value="<?= htmlspecialchars((string)$editProduct["description"]) ?>">
                        </div>
                        <div class="col-md-4 mx-auto">
                            <label class="form-label" for="f">New photo (optional)</label>
                            <input id="f" type="file" name="image_file" class="d-none" accept="image/*">

                            <div class="input-group">
                            <label class="btn btn-outline-secondary" for="f">Choose file</label>
                            <input id="fn" class="form-control" value="No file chosen" readonly>
                            </div>

                            <script>
                            f.onchange = () => fn.value = f.files[0]?.name || "No file chosen";
                            </script>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Save</button>
                    <a href="admin_products.php" class="btn btn-secondary mt-3">Cancel</a>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
</main>
<footer class="text-center p-3 mt-5">
    © 2026 Copyright: Chairway
</footer>
</body>
</html>