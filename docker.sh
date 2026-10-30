#!/bin/bash

set -e

# Spróbuj ustalić, której wersji compose używać
if docker compose version >/dev/null 2>&1; then
    DOCKER_COMPOSE="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
    DOCKER_COMPOSE="docker-compose"
else
    echo "Błąd: Brak 'docker compose' lub 'docker-compose'. Zainstaluj Docker Compose."
    exit 1
fi

COMPOSE_FILE="docker-compose.yml"

if [ ! -f "$COMPOSE_FILE" ]; then
    echo "Błąd: Nie znaleziono pliku $COMPOSE_FILE"
    exit 1
fi

usage() {
    echo "Użycie: $0 {build|up|down|clean|rebuild}"
    exit 1
}

if [ "$#" -ne 1 ]; then
    usage
fi

case "$1" in
    build)
        $DOCKER_COMPOSE -f "$COMPOSE_FILE" build
        ;;
    up)
        $DOCKER_COMPOSE -f "$COMPOSE_FILE" up -d
        echo "✅ Uruchomiono."
        ;;
    down)
        $DOCKER_COMPOSE -f "$COMPOSE_FILE" down
        echo "⏹️ Zatrzymano."
        ;;
    clean)
        echo "🧹 Czyszczenie..."
        $DOCKER_COMPOSE -f "$COMPOSE_FILE" down --volumes --remove-orphans 2>/dev/null || true
        # Usuń obrazy z compose
        $DOCKER_COMPOSE -f "$COMPOSE_FILE" images -q | xargs -r docker rmi -f
        docker system prune -af --volumes
        echo "✨ Wyczyszczono."
        ;;
    rebuild)
        $0 down
        $0 build
        $0 up
        ;;
    *)
        usage
        ;;
esac
