<?php
/*
 * One-time fix: makes public/storage a REAL directory (not a symlink) and copies over
 * anything already written to storage/app/public (e.g. an uploaded logo from before this fix).
 * Needed because this host's PHP has symlink() disabled/unreliable, which is why the earlier
 * storage-link.php attempt produced a dead link and the logo 403'd.
 *
 * DELETE THIS FILE FROM THE SERVER right after running it once — it manipulates the
 * filesystem and has no authentication.
 */

header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

$publicStorage = __DIR__.'/storage';
$sourcePublic = __DIR__.'/../storage/app/public';

echo "=== fix-storage.php ===\n\n";
echo "public/storage      : $publicStorage\n";
echo "storage/app/public  : $sourcePublic\n\n";

// 1. If public/storage is a dead/live symlink from the earlier attempt, remove it — we want a real folder.
if (is_link($publicStorage)) {
    unlink($publicStorage);
    echo "- Removed existing symlink at public/storage\n";
}

// 2. Create the real directory.
if (! is_dir($publicStorage)) {
    if (! mkdir($publicStorage, 0755, true)) {
        echo "FATAL: could not create public/storage (check permissions)\n";
        exit;
    }
    echo "+ Created real directory public/storage\n";
} else {
    echo "= public/storage already exists as a real directory\n";
}

// 3. Copy every file already under storage/app/public (old uploads) into public/storage.
function copyRecursive(string $from, string $to): int
{
    $copied = 0;
    if (! is_dir($from)) {
        return $copied;
    }

    foreach (scandir($from) as $item) {
        if ($item === '.' || $item === '..' || $item === '.gitignore') {
            continue;
        }

        $src = $from.'/'.$item;
        $dst = $to.'/'.$item;

        if (is_dir($src)) {
            if (! is_dir($dst)) {
                mkdir($dst, 0755, true);
            }
            $copied += copyRecursive($src, $dst);
        } elseif (copy($src, $dst)) {
            $copied++;
            echo "  copied: $item\n";
        } else {
            echo "  FAILED to copy: $item\n";
        }
    }

    return $copied;
}

echo "\nCopying existing files from storage/app/public...\n";
$count = copyRecursive($sourcePublic, $publicStorage);
echo "\nTotal files copied: $count\n";

echo "\n=== VERIFICATION ===\n";
echo 'is_dir(public/storage)  : '.(is_dir($publicStorage) ? 'YES' : 'NO')."\n";
echo 'is_link(public/storage) : '.(is_link($publicStorage) ? 'YES (unexpected)' : 'NO (correct)')."\n";
echo 'writable                : '.(is_writable($publicStorage) ? 'YES' : 'NO')."\n";

echo "\nDone. DELETE THIS FILE NOW.\n";
