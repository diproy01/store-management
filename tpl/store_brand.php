<?php

function renderStoreBrand(string $id, mixed $logo = null): void
{
    echo '<span id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" class="fr store-brand">';

    if (is_string($logo) && $logo !== '' && basename($logo) === $logo
        && is_file(__DIR__ . '/../upload/' . $logo)) {
        echo '<img src="upload/' . rawurlencode($logo) . '" alt="Store logo">';
    } else {
        echo 'Store Management';
    }

    echo '</span>';
}
