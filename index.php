<?php
// This root index.php is kept as a safe redirect to the canonical public webroot.
// The original file is backed up at `backup/original_index.php`.

header('Location: ./public/');
exit;
