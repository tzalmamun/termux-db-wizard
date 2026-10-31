#!/data/data/com.termux/files/usr/bin/bash
set -e

REPO_DIR="$(cd "$(dirname "$0")" && pwd)"
HOME_DIR="$HOME"
BIN_DIR="$HOME_DIR/bin"

echo "[1/5] Checking required commands..."
for cmd in php mariadb; do
  if ! command -v "$cmd" >/dev/null 2>&1; then
    echo "Missing: $cmd"
    echo "Run: pkg install php mariadb -y"
    exit 1
  fi
done

if ! php -m | grep -qi '^mysqli$'; then
  echo "WARNING: PHP mysqli extension is not enabled."
  echo "The wizard will not work until mysqli is available in PHP."
fi

echo "[2/5] Installing wizard files..."
mkdir -p "$HOME_DIR/dbwizard" "$BIN_DIR"
cp -f "$REPO_DIR/wizard/index.php" "$HOME_DIR/dbwizard/index.php"

if [ ! -f "$HOME_DIR/dbwizard/config.php" ]; then
  cp "$REPO_DIR/wizard/config.example.php" "$HOME_DIR/dbwizard/config.php"
  chmod 600 "$HOME_DIR/dbwizard/config.php"
  echo "Created ~/dbwizard/config.php. Edit it and change the admin password."
else
  echo "Keeping existing ~/dbwizard/config.php"
fi

echo "[3/5] Installing commands..."
cp -f "$REPO_DIR/scripts/dbw" "$BIN_DIR/dbw"
cp -f "$REPO_DIR/scripts/pma" "$BIN_DIR/pma"
chmod +x "$BIN_DIR/dbw" "$BIN_DIR/pma"

echo "[4/5] Adding ~/bin to PATH..."
touch "$HOME_DIR/.bashrc"
grep -q 'export PATH="$HOME/bin:$PATH"' "$HOME_DIR/.bashrc" || \
  echo 'export PATH="$HOME/bin:$PATH"' >> "$HOME_DIR/.bashrc"

echo "[5/5] Done."
echo
echo "Run: source ~/.bashrc"
echo "Edit settings: nano ~/dbwizard/config.php"
echo "Start wizard: dbw on"
echo "Wizard URL: http://127.0.0.1:8082"
echo
echo "Optional phpMyAdmin command: pma on"
