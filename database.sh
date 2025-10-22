#!/bin/bash

PROJECT_PATH="/home/smith/Proyectos/enlac/backend"
DB_NAME="enlac"
SQL_FILE="/home/smith/Descargas/data_pete75ru__enlac.sql"
DB_USER="dev"
DB_PASS="espartaD3vs2k20!"

# --- Inicio de la Ejecución ---

echo "⚙️  Iniciando configuración de la base de datos de Laravel..."
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
    mysql -u "$DB_USER" -p "$DB_PASS" "$DB_NAME" < "$SQL_FILE"
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