#!/bin/sh
set -eu

# Wrapper to start mysqld_exporter using Docker secrets for password
# Expects secret at /run/secrets/db_password (created via `docker secret create`)

DB_USER="${DB_USER:-sportzone}"
DB_HOST="${DB_HOST:-mysql}"
DB_PORT="${DB_PORT:-3306}"

if [ -f /run/secrets/db_password ]; then
  DB_PASS=$(cat /run/secrets/db_password)
else
  DB_PASS="${DB_PASS:-}"
fi

if [ -z "$DB_PASS" ]; then
  echo "[mysqld-exporter] WARNING: DB password is empty or not provided via secret" >&2
fi

DSN="${DB_USER}:${DB_PASS}@tcp(${DB_HOST}:${DB_PORT})/"
export DATA_SOURCE_NAME="$DSN"

echo "[mysqld-exporter] Starting with DSN user=${DB_USER} host=${DB_HOST} port=${DB_PORT}"

exec /bin/mysqld_exporter "$@"
