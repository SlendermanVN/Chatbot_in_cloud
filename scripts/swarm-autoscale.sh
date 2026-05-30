#!/usr/bin/env bash

set -euo pipefail

# Simple Swarm autoscaler for the web service.
# It uses CPU pressure as a practical proxy for request spikes.
# Run this on the Swarm manager host where Docker CLI is available.

STACK_NAME="${STACK_NAME:-sportzone}"
SERVICE_NAME="${SERVICE_NAME:-web}"
FULL_SERVICE_NAME="${STACK_NAME}_${SERVICE_NAME}"
MIN_REPLICAS="${MIN_REPLICAS:-2}"
MAX_REPLICAS="${MAX_REPLICAS:-6}"
SCALE_UP_THRESHOLD="${SCALE_UP_THRESHOLD:-70}"
SCALE_DOWN_THRESHOLD="${SCALE_DOWN_THRESHOLD:-25}"
POLL_INTERVAL="${POLL_INTERVAL:-20}"
UP_STREAK_REQUIRED="${UP_STREAK_REQUIRED:-3}"
DOWN_STREAK_REQUIRED="${DOWN_STREAK_REQUIRED:-5}"

up_streak=0
down_streak=0

get_replica_count() {
  docker service ls --format '{{.Name}} {{.Replicas}}' \
    | awk -v service="${FULL_SERVICE_NAME}" '$1 == service { split($2, parts, "/"); print parts[1] }'
}

get_average_cpu() {
  local containers
  containers=$(docker ps --filter "label=com.docker.swarm.service.name=${FULL_SERVICE_NAME}" -q)

  if [[ -z "${containers}" ]]; then
    echo "0"
    return
  fi

  docker stats --no-stream --format '{{.CPUPerc}}' ${containers} \
    | tr -d '%' \
    | awk 'BEGIN { sum=0; count=0 } { if ($1 ~ /^[0-9.]+$/) { sum += $1; count += 1 } } END { if (count == 0) print 0; else print sum / count }'
}

scale_service() {
  local target_replicas="$1"
  docker service scale "${FULL_SERVICE_NAME}=${target_replicas}" >/dev/null
  echo "Scaled ${FULL_SERVICE_NAME} to ${target_replicas} replicas"
}

echo "Watching ${FULL_SERVICE_NAME} for CPU pressure..."

while true; do
  current_replicas=$(get_replica_count)
  current_replicas=${current_replicas:-0}
  avg_cpu=$(get_average_cpu)

  printf '[%s] replicas=%s avg_cpu=%.2f%%\n' "$(date '+%Y-%m-%d %H:%M:%S')" "${current_replicas}" "${avg_cpu}"

  if awk -v cpu="${avg_cpu}" -v threshold="${SCALE_UP_THRESHOLD}" 'BEGIN { exit !(cpu >= threshold) }'; then
    up_streak=$((up_streak + 1))
    down_streak=0
  elif awk -v cpu="${avg_cpu}" -v threshold="${SCALE_DOWN_THRESHOLD}" 'BEGIN { exit !(cpu <= threshold) }'; then
    down_streak=$((down_streak + 1))
    up_streak=0
  else
    up_streak=0
    down_streak=0
  fi

  if [[ ${up_streak} -ge ${UP_STREAK_REQUIRED} && ${current_replicas} -lt ${MAX_REPLICAS} ]]; then
    scale_service $((current_replicas + 1))
    up_streak=0
    down_streak=0
  fi

  if [[ ${down_streak} -ge ${DOWN_STREAK_REQUIRED} && ${current_replicas} -gt ${MIN_REPLICAS} ]]; then
    scale_service $((current_replicas - 1))
    up_streak=0
    down_streak=0
  fi

  sleep "${POLL_INTERVAL}"
done