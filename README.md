# MyLocalHostApp

Una aplicación en PHP para gestionar proyectos locales con iconos personalizados.

## Instalación

1. Clonar el repositorio
2. Configurar la base de datos en `config.php`
3. Importar la base de datos desde el script:

```sql
CREATE DATABASE IF NOT EXISTS mylocalhostapp;

USE mylocalhostapp;

CREATE TABLE IF NOT EXISTS projects_address (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(100) NOT NULL,
    project_path VARCHAR(255) NOT NULL,
    project_icon VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
4. Iniciar el servidor local y acceder al proyecto.

## Funcionalidades

- Crear proyectos con icono personalizado.
- Listar proyectos en la interfaz.
- Buscar proyectos en tiempo real.
- Eliminar proyectos con confirmación.
