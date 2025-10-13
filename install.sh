#!/bin/bash

set -euo pipefail

# Minimal curl-installable bootstrapper for Traffic-UI
# Usage:
#   bash <(curl -Ls https://raw.githubusercontent.com/ScriptCascade/traffic-ui-open-source/main/install.sh)

require_cmd() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "Missing required command: $1" >&2
    return 1
  fi
}

ensure_apt_tools() {
  if ! command -v apt >/dev/null 2>&1; then
    echo "This installer currently supports Debian/Ubuntu (apt) only." >&2
    exit 1
  fi

  if [ "${EUID:-$(id -u)}" -ne 0 ]; then
    if command -v sudo >/dev/null 2>&1; then
      SUDO="sudo"
    else
      echo "Please run as root or install sudo." >&2
      exit 1
    fi
  else
    SUDO=""
  fi

  $SUDO apt update -y >/dev/null 2>&1 || true
  $SUDO apt install -y curl unzip >/dev/null 2>&1 || true
}

main() {
  ensure_apt_tools

  TMP_DIR="$(mktemp -d)"
  BRANCH="${TRAFFIC_UI_BRANCH:-main}"
  REPO_ZIP_URL="https://github.com/ScriptCascade/traffic-ui-open-source/archive/refs/heads/${BRANCH}.zip"

  echo "Downloading Traffic-UI (${BRANCH})..."
  curl -fsSL "$REPO_ZIP_URL" -o "$TMP_DIR/repo.zip"

  echo "Extracting..."
  unzip -q "$TMP_DIR/repo.zip" -d "$TMP_DIR"
  EXTRACT_DIR="$(unzip -Z -1 "$TMP_DIR/repo.zip" | head -n1 | cut -d/ -f1)"
  cd "$TMP_DIR/$EXTRACT_DIR"

  if [ ! -f install-traffic-ui.sh ]; then
    echo "install-traffic-ui.sh not found in repository root" >&2
    exit 1
  fi

  chmod +x install-traffic-ui.sh
  echo "Running Traffic-UI installer..."
  bash ./install-traffic-ui.sh

  echo "Cleaning up..."
  rm -rf "$TMP_DIR" || true

  echo "Done."
}

main "$@"

