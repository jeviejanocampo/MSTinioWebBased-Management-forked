<?php
function generateAppKey() {
    return bin2hex(random_bytes(16)); // 32 characters long
}

function generateAppSecret() {
    return bin2hex(random_bytes(32)); // 64 characters long
}

// Example usage
$appKey = generateAppKey();
$appSecret = generateAppSecret();

echo "App Key: $appKey\n";
echo "App Secret: $appSecret\n";
?>
