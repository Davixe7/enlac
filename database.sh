#!/bin/bash

# ====================================================================
# CONFIGURACIÓN DEL PROYECTO Y BASE DE DATOS
# ====================================================================

# 1. Ruta absoluta o relativa a tu proyecto Laravel
PROJECT_PATH="/home/smith/Proyectos/enlac/backend"

# 2. Nombre de la base de datos de MySQL (debe coincidir con .env)
DB_NAME="enlac"

# 3. Ruta al archivo .sql que quieres importar
SQL_FILE="/home/smith/Descargas/data_pete75ru_enlac.sql"

# 4. Credenciales de MySQL para localhost
DB_USER="dev"
# Si tu usuario root tiene contraseña, descomenta la siguiente línea y ponla.
# NOTA: Pasar la contraseña directamente en el script es menos seguro.
# DB_PASS="mi_contraseña_secreta" 
DB_PASS="espartaD3vs2k20!" # Deja vacío si no tienes contraseña de root


# --- Inicio de la Ejecución ---

echo "⚙️  Iniciando configuración de la base de datos de Laravel..."

# 1. Entrar al directorio del proyecto Laravel
echo "➡️  Cambiando al directorio: $PROJECT_PATH"
cd "$PROJECT_PATH"

if [ $? -ne 0 ]; then
    echo "❌ ERROR: No se pudo entrar al directorio '$PROJECT_PATH'. Verifica la ruta."
    exit 1
fi

# 2. Ejecutar php artisan migrate:fresh --seed
echo "🚀 Ejecutando php artisan migrate:fresh --seed..."
php artisan migrate:fresh --seed

if [ $? -ne 0 ]; then
    echo "❌ ERROR: Falló la ejecución de 'php artisan migrate:fresh --seed'. Revisa la configuración de tu .env."
    cd - > /dev/null
    exit 1
fi

echo "✅ Migraciones y Seeds completados."


# 3. Importar el archivo .sql
echo "💾 Importando datos desde $SQL_FILE en la base de datos $DB_NAME..."

# Comando de importación de MySQL
# Usamos un if para manejar la contraseña de forma condicional
if [ -n "$DB_PASS" ]; then
    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$SQL_FILE"
else
    mysql -u "$DB_USER" "$DB_NAME" < "$SQL_FILE"
fi

if [ $? -ne 0 ]; then
    echo "❌ ERROR: Falló la importación del archivo SQL. Asegúrate de que el archivo existe y las credenciales son correctas."
    cd - > /dev/null
    exit 1
fi

echo "✅ Importación de datos (.sql) completada exitosamente."

# Volver al directorio original (opcional, pero buena práctica)
cd - > /dev/null

echo ""
echo "🎉 ¡Configuración de la base de datos finalizada con éxito!"