# SistemaInventarios v4
SistemaInventarios es un sistema de Inventario y Ventas de proposito general desarrollado con PHP y MySQL.

## Modulos
- Productos
- Categorias
- Caja
- Clientes
- Proveedores
- Inventario
- Usuarios

## Update v4 2023
- Se actualizo la Plantilla Principal por Core UI v4


## Instalacion
Para instalar el Sistema Requieres Apache+PHP+MySQL o tener instalado el XAMPP/LAMPP

1. Primero debes descargar este repositorio y colocarlo en tu carpeta htdocs o /var/www/ segun sea el caso.
2. Deberas crear la base de datos llamada SistemaInventarios en tu servidor mysql, las tablas requeridas estan en el archivo schema.sql
3. Deberas modificar el archivo inventio-lite/core/controller/Database.php y agregar los datos de conexion a tu base de datos.
4. Ejecutar el sistema desde http://localhost/inventio-lite/ depende del nombre que le pusiste a la carpeta del proyecto.
5. Los datos de usuario por default son:
    Usuario: admin
    Password: admin
6. DIsfrutar el sistema