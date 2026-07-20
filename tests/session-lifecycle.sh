#!/bin/sh
set -eu

project_root=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
session_test_dir=$(mktemp -d /tmp/aqi-session-test.XXXXXX)

cleanup() {
    rm -rf -- "$session_test_dir"
}
trap cleanup EXIT INT TERM

AQI_CONFIG="$project_root/config-DEFAULT.php" \
AQI_PATH="$project_root/src/htdocs" \
AQI_TEST_BOOT="$project_root/src/htdocs/boot.php" \
php -r '
    require getenv("AQI_TEST_BOOT");
    if (session_status() !== PHP_SESSION_NONE) {
        fwrite(STDERR, "boot.php started a session for a stateless request\n");
        exit(1);
    }
'

touch "$session_test_dir/sess_expired"
touch "$session_test_dir/sess_fresh"
touch "$session_test_dir/unrelated"
touch -d '1 hour ago' "$session_test_dir/sess_expired" "$session_test_dir/unrelated"

AQI_SESSION_PATH="$session_test_dir" \
AQI_SESSION_MAX_LIFETIME=1440 \
php "$project_root/src/cli/cleanup-sessions.php"

test ! -e "$session_test_dir/sess_expired"
test -e "$session_test_dir/sess_fresh"
test -e "$session_test_dir/unrelated"

echo "Session lifecycle tests passed."
