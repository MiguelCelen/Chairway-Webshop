<?php
require_once "config.php";

if (!User::isLoggedIn() || !User::isAdmin()) {
    header("Location: login.php");
    exit;
}

$pdo = Database::getConnection();
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "add") {
        $title       = trim($_POST["title"] ?? "");
        $price       = (float)($_POST["price"] ?? 0);
        $category    = trim($_POST["category"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $image       = "";

        if ($title === "" || $price <= 0) {
            $error = "Titel en prijs zijn verplicht.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO products (title, price, category, image, description)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $price, $category, $image, $description]);
            $success = "Product toegevoegd.";
        }

    } 
    elseif ($action === "update") {
        $id          = (int)($_POST["id"] ?? 0);
        $title       = trim($_POST["title"] ?? "");
        $price       = (float)($_POST["price"] ?? 0);
        $category    = trim($_POST["category"] ?? "");
        $description = trim($_POST["description"] ?? "");

        if ($id <= 0 || $title === "" || $price <= 0) {
            $error = "Ongeldige productgegevens.";
        } else {
            $stmt = $pdo->prepare("
                UPDATE products
                SET title = ?, price = ?, category = ?, description = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $price, $category, $description, $id]);
            $success = "Product bijgewerkt.";
        }

    } 
    elseif ($action === "delete") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Product verwijderd.";
        } else {
            $error = "Ongeldig product.";
        }
    }
}

$editProduct = null;
if (isset($_GET["edit"])) {
    $editId = (int)$_GET["edit"];
    if ($editId > 0) {
        $stmt = $pdo->prepare("
            SELECT id, title, price, category, description
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
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Producten beheren</title>
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
        <li class="nav-item"><a class="nav-link active" href="admin_products.php">Admin producten</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_product_images.php">Product afbeeldingen</a></li>
      </ul>
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item d-flex align-items-center me-2">
            <span class="navbar-text small">
                <?= htmlspecialchars((string)("Hallo, " . $user["name"] ?? "")) ?>
            </span>
        </li>
        <li class="nav-item"><a class="nav-link" href="handlers/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<main>
<div class="container my-4">
    <h1 class="mb-4">Producten beheren</h1>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Nieuw product toevoegen</div>
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="action" value="add">
                <div class="row g-2">
                    <div class="col-12 col-md-3">
                        <label class="form-label">Titel</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Prijs (€)</label>
                        <input type="number" name="price" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Categorie</label>
                        <input type="text" name="category" class="form-control">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Beschrijving</label>
                        <input type="text" name="description" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-dark mt-3">Product toevoegen</button>
            </form>
        </div>
    </div>

    <h2 class="h4 mb-3">Bestaande producten</h2>
    <div class="table-responsive">
    <table class="table table-striped align-middle table-sm">
        <thead>
        <tr>
            <th class="d-none d-md-table-cell">ID</th>
            <th>Titel</th>
            <th class="d-none d-sm-table-cell">Categorie</th>
            <th>Prijs</th>
            <th>Acties</th>
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
                        Bewerken
                    </a>

                    <form method="post" onsubmit="return confirm('Zeker verwijderen?');" class="w-100 w-md-auto">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$p["id"] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 w-md-auto">
                            Verwijderen
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
                Product bewerken #<?= (int)$editProduct["id"] ?>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= (int)$editProduct["id"] ?>">

                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Titel</label>
                            <input type="text" name="title" class="form-control"
                                   value="<?= htmlspecialchars((string)$editProduct["title"]) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Prijs (€)</label>
                            <input type="number" name="price" class="form-control" min="0" step="0.01"
                                   value="<?= htmlspecialchars((string)$editProduct["price"]) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Categorie</label>
                            <input type="text" name="category" class="form-control"
                                   value="<?= htmlspecialchars((string)$editProduct["category"]) ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Beschrijving</label>
                            <input type="text" name="description" class="form-control"
                                   value="<?= htmlspecialchars((string)$editProduct["description"]) ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Opslaan</button>
                    <a href="admin_products.php" class="btn btn-secondary mt-3">Annuleren</a>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
</main>
<footer class="text-center p-3 mt-5">
    © 2025 Copyright: Chairway
</footer>
</body>
</html>