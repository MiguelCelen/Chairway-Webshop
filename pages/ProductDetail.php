<?php
require_once "../config.php";

$pdo  = Database::getConnection();
$user = User::currentUser();
$productId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($productId <= 0) {
    echo "Invalid product.";
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, title, price, category, image, description
    FROM products
    WHERE id = ?
");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found.";
    exit;
}

$commentsStmt = $pdo->prepare("
    SELECT c.message, c.rating, c.created_at,
           COALESCE(u.name, u.email) AS author
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.product_id = ?
    ORDER BY c.created_at DESC
");
$commentsStmt->execute([$productId]);
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../Assets/Images/logo_chairway.png" type="icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string)$product["title"]) ?> - Chairway</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">CHAIRWAY</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="Artikelen.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="Winkelwagen.php">Shopping cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="ContactUs.php">Contact us</a></li>
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if ($user): ?>
                        <li class="nav-item d-flex align-items-center me-2">
                            <span class="navbar-text small">
                                Hello, <?= htmlspecialchars((string)($user["name"] ?? $user["email"])) ?>
                            </span>
                        </li>

                        <?php if (User::isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../admin_products.php">Changes</a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a class="nav-link" href="../handlers/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="../login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="../register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main>
    <div class="container" style="padding-top: 2rem;">
    <a href="Artikelen.php" class="btn btn-secondary mb-3 w-10 w-md-auto">← Back</a>

    <?php if (isset($_GET["error"])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string)$_GET["error"]) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET["success"])): ?>
        <div class="alert alert-success"><?= htmlspecialchars((string)$_GET["success"]) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-12 col-md-6">
            <?php if (!empty($product["image"])): ?>
                <img src="<?= htmlspecialchars((string)$product["image"]) ?>"
                     alt="<?= htmlspecialchars((string)$product["title"]) ?>"
                     class="img-fluid mb-3">
            <?php else: ?>
                <div class="bg-secondary" style="width:100%;height:300px;"></div>
            <?php endif; ?>
        </div>

        <div class="col-12 col-md-6">
            <h1><?= htmlspecialchars((string)$product["title"]) ?></h1>
            <p class="lead">
                Category: <?= htmlspecialchars((string)$product["category"]) ?><br>
                <strong>€ <?= number_format((float)$product["price"], 2, ",", ".") ?></strong>
            </p>
            <p><?= nl2br(htmlspecialchars((string)($product["description"] ?? ""))) ?></p>

            <div class="mt-4">
                <h5 class="mb-3">Buy</h5>

                <div class="d-flex flex-column flex-sm-row gap-2 align-items-start align-items-sm-end">
                    <form method="post" action="../handlers/add_to_cart.php" class="d-flex flex-column flex-sm-row gap-2 align-items-start align-items-sm-end">
                        <input type="hidden" name="product_id" value="<?= (int)$productId ?>">
                        <div>
                            <label class="form-label mb-1">Quantity</label>
                            <input type="number" name="quantity" value="1" min="1" class="form-control w-100 w-sm-auto" style="max-width:120px;">
                        </div>
                        <button type="submit" class="btn btn-outline-dark w-100 w-sm-auto">Add to shopping cart.</button>
                    </form>
                </div>

                <?php if (!$user): ?>
                    <small class="text-warning d-block mt-2">
                        You are not logged in.
                    </small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <h3>Comments</h3>
    <div id="commentStatus" class="mb-3"></div>
    <div class="mb-4" id="commentList">
        <?php if (empty($comments)): ?>
            <p id="noComments">There are no comments for this product yet.</p>
        <?php else: ?>
            <?php foreach ($comments as $c): ?>
                <div class="border rounded p-3 mb-3 bg-white">
                    <strong><?= htmlspecialchars((string)($c["author"] ?? "Onbekende gebruiker")) ?></strong>
                    – Rating: <?= (int)$c["rating"] ?>/5<br>
                    <small class="text-muted"><?= htmlspecialchars((string)$c["created_at"]) ?></small>
                    <p class="mb-0"><?= nl2br(htmlspecialchars((string)($c["message"] ?? ""))) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if ($user): ?>
        <h4>Write a comment</h4>

        <form id="commentForm" method="post" action="../handlers/add_comment.php" class="mb-5">
            <input type="hidden" name="product_id" value="<?= (int)$productId ?>">

            <div class="mb-2 w-100" style="max-width: 200px;">
                <label class="form-label">Rating</label>
                <select name="rating" class="form-select">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label">Comment</label>
                <textarea name="message" class="form-control" rows="3" required></textarea>
            </div>

            <button type="submit" class="btn btn-dark w-10 w-md-auto">Send</button>
        </form>
    <?php else: ?>
        <p>Please log in to post a comment.</p>
    <?php endif; ?>
</div>
</main>
<footer class="text-center p-3 mt-5">
    © 2026 Copyright: Chairway
</footer>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("commentForm");
    const list = document.getElementById("commentList");
    const status = document.getElementById("commentStatus");
    const noComments = document.getElementById("noComments");

    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        status.innerHTML = "";
        const fd = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: "POST",
                body: fd,
                headers: { "Accept": "application/json" }
            });

            const data = await res.json();

            if (!data.success) {
                status.innerHTML = `<div class="alert alert-danger">${escapeHtml(data.error || "Fout bij comment plaatsen")}</div>`;
                return;
            }

            if (noComments) noComments.remove();

            const div = document.createElement("div");
            div.className = "border rounded p-2 mb-2 bg-white";
            div.innerHTML = `
                <strong>${escapeHtml(data.comment.author)}</strong>
                – Rating: ${Number(data.comment.rating)}/5<br>
                <small class="text-muted">${escapeHtml(data.comment.created_at)}</small>
                <p class="mb-0">${escapeHtml(data.comment.message).replaceAll("\\n", "<br>")}</p>
            `;
            list.prepend(div);

            status.innerHTML = `<div class="alert alert-success">Comment geplaatst ✅</div>`;
            form.reset();

        } catch (err) {
            status.innerHTML = `<div class="alert alert-danger">Netwerk/JSON fout: ${escapeHtml(String(err))}</div>`;
        }
    });

    function escapeHtml(str) {
        return String(str)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }
});
</script>
</body>
</html>