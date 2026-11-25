<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/tpl/store_brand.php';

$fields = [
    'sname' => ['Store name', 'name'],
    'address' => ['Address', 'address'],
    'place' => ['Place', 'place'],
    'city' => ['City', 'city'],
    'pin' => ['Postal code', 'pin'],
    'phone' => ['Phone', 'phone'],
    'website' => ['Website', 'web'],
    'email' => ['Email', 'email'],
];
$store = $db->queryUniqueObject('SELECT * FROM store_details');
$values = [];
foreach ($fields as $input => [$label, $column]) {
    $values[$input] = $store->$column ?? '';
}
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$_SESSION['store_details_token'] ??= bin2hex(random_bytes(32));
$isSetup = !empty($_SESSION['initial_store_setup']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['store_details_token'], $token)) {
        http_response_code(403);
        exit('Reload Store Settings and try again.');
    }

    $logo = $store->log ?? '';
    $logoType = $store->type ?? '';
    $newLogo = null;
    try {
        foreach ($values as $input => $previous) {
            $value = $_POST[$input] ?? '';
            if (!is_string($value) || preg_match('/\A.{0,100}\z/us', $value) !== 1) {
                throw new InvalidArgumentException('Use up to 100 characters in each store field.');
            }
            $values[$input] = trim($value);
        }
        if ($values['sname'] === '') {
            throw new InvalidArgumentException('Enter your store name.');
        }

        $file = $_FILES['file'] ?? null;
        if ($file !== null && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $image = $file['error'] === UPLOAD_ERR_OK && $file['size'] <= 2 * 1024 * 1024
                ? @getimagesize($file['tmp_name']) : false;
            $formats = [IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_GIF => 'gif'];
            $extension = $image ? ($formats[$image[2]] ?? null) : null;
            if ($extension === null) {
                throw new InvalidArgumentException('Choose a PNG, JPEG, or GIF image up to 2 MB.');
            }
            $logo = bin2hex(random_bytes(16)) . '.' . $extension;
            $logoType = $image['mime'];
            $path = __DIR__ . '/upload/' . $logo;
            if (!move_uploaded_file($file['tmp_name'], $path)) {
                throw new InvalidArgumentException('PHP could not write to the upload directory. Check its permissions, or save without choosing a logo.');
            }
            $newLogo = $path;
        }

        $statement = $db->connection->prepare(
            'UPDATE store_details SET name=?, address=?, place=?, city=?, pin=?, phone=?, web=?, email=?, log=?, type=?'
        );
        $statement->execute([...array_values($values), $logo, $logoType]);
        $_SESSION['logo'] = $logo;
        unset($_SESSION['initial_store_setup']);
        header('Location: ' . ($isSetup ? 'dashboard.php' : 'update_details.php?saved=1'), true, 303);
        exit;
    } catch (InvalidArgumentException | mysqli_sql_exception $exception) {
        if ($newLogo !== null) {
            unlink($newLogo);
        }
        if ($exception instanceof mysqli_sql_exception) {
            error_log('Store Management settings: ' . $exception->getMessage());
            $error = 'Unable to save store details. Check the PHP error log.';
        } else {
            $error = $exception->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Store Management - Store Settings</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cmxform.css">
</head>
<body>
<div id="header">
    <div class="page-full-width cf">
        <h1 class="fl">Store Settings</h1>
        <?php renderStoreBrand('company-branding', $store->log ?? null); ?>
    </div>
</div>
<div id="content">
    <?php if ($error !== ''): ?>
        <p class="error-box round" role="alert"><?= $escape($error) ?></p>
    <?php elseif (isset($_GET['saved'])): ?>
        <p class="confirmation-box round" role="status">Store details saved.</p>
    <?php endif; ?>
    <form method="post" id="login-form" class="cmxform" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= $escape($_SESSION['store_details_token']) ?>">
        <?php foreach ($fields as $input => [$label]): ?>
            <p>
                <label for="<?= $input ?>"><?= $label ?></label>
                <input type="text" name="<?= $input ?>" id="<?= $input ?>" maxlength="100"
                       class="round full-width-input" value="<?= $escape($values[$input]) ?>"
                       <?= $input === 'sname' ? 'required' : '' ?>>
            </p>
        <?php endforeach; ?>
        <p>
            <label for="file">Store logo (optional)</label>
            <input type="file" name="file" id="file" accept="image/png,image/jpeg,image/gif">
        </p>
        <p>
            <button type="submit" class="button round blue"><?= $isSetup ? 'Finish setup' : 'Save changes' ?></button>
            <a href="dashboard.php" class="button blue round">Dashboard</a>
        </p>
    </form>
</div>
</body>
</html>
