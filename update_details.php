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
$logos = [];
foreach (glob(__DIR__ . '/upload/*') ?: [] as $path) {
    $name = basename($path);
    if (!preg_match('/\A[A-Za-z0-9][A-Za-z0-9_. -]{0,94}\.(?:png|jpe?g|gif)\z/i', $name)
        || !is_file($path) || is_link($path)) {
        continue;
    }
    $image = @getimagesize($path);
    if ($image && in_array($image[2], [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF], true)) {
        $logos[$name] = $image['mime'];
    }
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['store_details_token'], $token)) {
        http_response_code(403);
        exit('Reload Store Settings and try again.');
    }

    $logo = $store->log ?? '';
    $logoType = $store->type ?? '';
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

        $selection = $_POST['logo'] ?? null;
        if ($selection !== null && $selection !== $logo) {
            if (!is_string($selection) || ($selection !== '' && !isset($logos[$selection]))) {
                throw new InvalidArgumentException('Select an available store logo.');
            }
            $logo = $selection;
            $logoType = $logos[$selection] ?? '';
        }

        $statement = $db->connection->prepare(
            'UPDATE store_details SET name=?, address=?, place=?, city=?, pin=?, phone=?, web=?, email=?, log=?, type=?'
        );
        $statement->execute([...array_values($values), $logo, $logoType]);
        $_SESSION['logo'] = $logo;
        header('Location: update_details.php?saved=1', true, 303);
        exit;
    } catch (InvalidArgumentException | mysqli_sql_exception $exception) {
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
    <form method="post" id="login-form" class="cmxform">
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
            <label for="logo">Store logo (optional)</label>
            <select id="logo" name="logo">
                <?php $currentLogo = $store->log ?? ''; ?>
                <option value="">No logo</option>
                <?php if ($currentLogo !== '' && !isset($logos[$currentLogo])): ?>
                    <option value="<?= $escape($currentLogo) ?>" selected>Keep current logo</option>
                <?php endif; ?>
                <?php foreach ($logos as $name => $mime): ?>
                    <option value="<?= $escape($name) ?>" <?= $name === $currentLogo ? 'selected' : '' ?>>
                        <?= $escape($name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <button type="submit" class="button round blue">Save changes</button>
            <a href="dashboard.php" class="button blue round">Dashboard</a>
        </p>
    </form>
</div>
</body>
</html>
