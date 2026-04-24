<?php
// Run once from CLI: php generate_hash.php
// Paste the output hash into .env as ADMIN_HASH=...
$password = readline('Enter admin password: ');
echo password_hash($password, PASSWORD_BCRYPT) . PHP_EOL;
