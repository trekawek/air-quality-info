<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

$sessionPath = getenv('AQI_SESSION_PATH');
if ($sessionPath === false || $sessionPath === '') {
    $sessionPath = ini_get('session.save_path');
}

$maxLifetime = getenv('AQI_SESSION_MAX_LIFETIME');
if ($maxLifetime === false || $maxLifetime === '') {
    $maxLifetime = ini_get('session.gc_maxlifetime');
}

if (!is_dir($sessionPath)) {
    fwrite(STDERR, "Session directory does not exist: {$sessionPath}\n");
    exit(1);
}

if (filter_var($maxLifetime, FILTER_VALIDATE_INT) === false || (int) $maxLifetime < 1) {
    fwrite(STDERR, "Invalid session lifetime: {$maxLifetime}\n");
    exit(1);
}

$cutoff = time() - (int) $maxLifetime;
$removed = 0;
$failed = 0;

foreach (new DirectoryIterator($sessionPath) as $file) {
    if ($file->isDot() || $file->isLink() || !$file->isFile()) {
        continue;
    }
    if (!preg_match('/^sess_[A-Za-z0-9,-]+$/D', $file->getFilename())) {
        continue;
    }
    $path = $file->getPathname();
    $modifiedAt = @filemtime($path);
    if ($modifiedAt === false || $modifiedAt >= $cutoff) {
        continue;
    }

    if (@unlink($path)) {
        $removed++;
    } else if (file_exists($path)) {
        $failed++;
        fwrite(STDERR, "Could not remove expired session: {$file->getFilename()}\n");
    }
}

if ($removed > 0) {
    fwrite(STDOUT, "Removed {$removed} expired session file(s).\n");
}

exit($failed === 0 ? 0 : 1);
