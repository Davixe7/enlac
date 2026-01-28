#!/bin/bash

# --- CONFIGURACIÓN DEL SERVIDOR REMOTO ---
REMOTE_HOST="sistemaenlac.com"
REMOTE_USER="pete75ru_enlac"
REMOTE_PASS="Myenlacdb2025"
DB_NAME="pete75ru_enlac"

# --- CONFIGURACIÓN LOCAL ---
LOCAL_USER="dev"
LOCAL_PASS="espartaD3vs2k20!"
LOCAL_DB="enlac"

# Ruta del directorio de backups
BACKUP_DIR="$HOME/proyectos/enlac/backups"
TIMESTAMP=$(date +"%Y%m%d%H%M%S")

# Nombres de archivos con timestamp
FULL_FILE="$BACKUP_DIR/${TIMESTAMP}_full.sql"
DATA_FILE="$BACKUP_DIR/${TIMESTAMP}_data.sql"

# --- CONFIGURACIÓN DE RESPALDO ---
# Tablas a excluir (separadas por coma y con el formato db.tabla)
EXCLUDE_TABLES="--ignore-table=$DB_NAME.logs --ignore-table=$DB_NAME.cache --ignore-table=$DB_NAME.migrations"

# --- LÓGICA DEL SCRIPT ---

# 1. Backup Completo (Estructura + Datos)
echo "📦 Iniciando descarga de backups a: $BACKUP_DIR"
{
    echo "SET FOREIGN_KEY_CHECKS=0;"
    mysqldump --no-tablespaces -h $REMOTE_HOST -u $REMOTE_USER -p$REMOTE_PASS $DB_NAME
    echo "SET FOREIGN_KEY_CHECKS=1;"
} > "$FULL_FILE"

# 2. Backup Solo Datos (Excluyendo tablas, con columnas) con FK Disabled
echo "  -> Generando backup de solo datos..."
{
    echo "SET FOREIGN_KEY_CHECKS=0;"
    mysqldump --no-tablespaces -h $REMOTE_HOST -u $REMOTE_USER -p$REMOTE_PASS \
        --no-create-info \
        --complete-insert \
        $EXCLUDE_TABLES \
        $DB_NAME
    echo "SET FOREIGN_KEY_CHECKS=1;"
} > "$DATA_FILE"

# 4. Importación basada en flag
# Uso: ./script.sh --full o ./script.sh --data
FLAG=$1
SELECTED_FILE=""

if [ "$FLAG" == "--full" ]; then
    SELECTED_FILE="$FULL_FILE"
elif [ "$FLAG" == "--data" ]; then
    SELECTED_FILE="$DATA_FILE"
else
    echo "⚠️  No se especificó un flag válido (--full o --data). Los archivos se guardaron pero no se importaron."
    exit 0
fi

if [ -f "$SELECTED_FILE" ]; then
    echo "🗑️  Limpiando base de datos local ($LOCAL_DB)..."
    mysql -u $LOCAL_USER -p$LOCAL_PASS -e "DROP DATABASE IF EXISTS $LOCAL_DB; CREATE DATABASE $LOCAL_DB;"
    
    echo "🚀 Importando $SELECTED_FILE..."
    mysql -u $LOCAL_USER -p$LOCAL_PASS $LOCAL_DB < "$SELECTED_FILE"
    echo "✅ Proceso completado con éxito."
else
    echo "❌ Error: El archivo no pudo ser generado."
fi

echo "✅ Proceso finalizado."
