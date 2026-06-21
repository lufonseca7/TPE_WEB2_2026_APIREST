<?php

require_once './config.php';


class productosModel{
    private $db;

    function __construct(){
        //PUERTO DE MYSQL MODIFICADO POR ERROR EN XAMMP: nuevo puerto 3307 y puerto original: 3306
        $this->db = new PDO('mysql:host=' . MYSQL_HOST . ';port=3307;dbname=' . MYSQL_DB . ';charset=utf8', MYSQL_USER, MYSQL_PASS);
        $this->_deploy();
    }

    private function _deploy()
    {
        $query = $this->db->query('SHOW TABLES');
        $tables = $query->fetchAll();

        $password = 'admin';
        $hashedpassword = password_hash($password, PASSWORD_BCRYPT);

        if (count($tables) == 0) {
            $sql = <<<END
            SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
            START TRANSACTION;
            SET time_zone = "+00:00";

            CREATE TABLE `categorias` (
              `id_categoria` int(11) NOT NULL,
              `nombre_categoria` varchar(70) NOT NULL,
              `descripcion` varchar(250) NOT NULL,
              `fecha_creacion` date NOT NULL DEFAULT current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

            INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `descripcion`, `fecha_creacion`) VALUES
            (1, 'Remeras', 'Remeras deportivas, urbanas y mas', '2026-05-11'),
            (3, 'Buzos', 'Buzos urbanos, tipo "High School", deportivos y mas', '2026-05-12'),
            (4, 'Pantalones', 'Pantalones de jean, deportivos, pantalones cortos y mas', '2026-05-11'),
            (5, 'Jersey\\'s', 'Jersey\\'s de futbol americano y mas', '2026-05-13');

            CREATE TABLE `productos` (
              `id` int(11) NOT NULL,
              `nombre_producto` varchar(70) NOT NULL,
              `categoria` int(11) NOT NULL,
              `precio` int(11) NOT NULL,
              `talle` varchar(5) NOT NULL,
              `color` varchar(15) NOT NULL,
              `marca` varchar(20) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

            INSERT INTO `productos` (`id`, `nombre_producto`, `categoria`, `precio`, `talle`, `color`, `marca`) VALUES
            (1, 'Jersey Nro 52', 5, 20000, 'XL', 'Verde', 'Rusell'),
            (2, 'Trackpant ', 4, 25000, 'L', 'Negro', 'Adidas'),
            (3, 'Hoodie Yankees', 3, 30000, 'L', 'Gris', 'NFL'),
            (4, 'Camiseta Barcelona Nro 10 Retro', 1, 34000, 'XL', 'Azul', 'Kappa'),
            (5, 'Hoodie retro', 3, 32000, 'XXL', 'Rojo', 'Nike'),
            (6, 'Chomba Polo ', 1, 18000, 'M', 'Negro', 'Polo'),
            (7, 'Jersey Nro 22', 5, 32000, 'L', 'Azul', 'Nike'),
            (8, 'Pantalon Rompeviento retro', 4, 29000, 'L', 'Azul', 'Nike');

            CREATE TABLE `usuario` (
              `id` int(11) NOT NULL,
              `user` varchar(50) NOT NULL,
              `password` char(60) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

            INSERT INTO `usuario` (`id`, `user`, `password`) VALUES
            (1, 'webadmin', '$hashedpassword');

            ALTER TABLE `categorias`
              ADD PRIMARY KEY (`id_categoria`);

            ALTER TABLE `productos`
              ADD PRIMARY KEY (`id`),
              ADD KEY `fk_productos_categorias` (`categoria`);

            ALTER TABLE `usuario`
              ADD PRIMARY KEY (`id`);

            ALTER TABLE `categorias`
              MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

            ALTER TABLE `productos`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

            ALTER TABLE `usuario`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

            ALTER TABLE `productos`
              ADD CONSTRAINT `fk_productos_categorias` FOREIGN KEY (`categoria`) REFERENCES `categorias` (`id_categoria`);

            COMMIT;
            END;

            $this->db->query($sql);
        }
    }

    function getProducts(){
      $query = $this->db->prepare('SELECT a.* , b.* FROM productos a LEFT JOIN  categorias b ON a.categoria = b.id_categoria');
      $query->execute();

      return $query->fetchAll(PDO::FETCH_OBJ);
    }

    function insertProduct($producto, $categoria, $precio, $talle, $color, $marca){
      $query = $this->db->prepare('INSERT INTO productos(nombre_producto, categoria, precio, talle, color, marca) VALUES (?,?,?,?,?,?)');
      $query->execute(array($producto, $categoria, $precio, $talle, $color, $marca));

      return $this->db->lastInsertId();
    }

    function removeProduct($id){
      $query = $this->db->prepare('DELETE FROM productos WHERE id = ?');
      $query->execute([$id]);
    }

    function filtrarProducto($id){
      $query = $this->db->prepare('SELECT a.*, b.* FROM productos a LEFT JOIN categorias b ON a.categoria= b.id_categoria  WHERE b.id_categoria = ?');
      $query->execute(array($id));

      return $query->fetchAll(PDO::FETCH_OBJ);

    }

    function showProduct($id){
      $query = $this->db->prepare('SELECT * FROM productos WHERE id = ?');
      $query->execute(array($id));
      return  $query->fetch(PDO::FETCH_OBJ);
    }

    function updateProduct($id, $nuevoNombre, $categoria, $precio, $talle, $color, $marca){
      $query = $this->db->prepare('UPDATE productos SET nombre_producto = ?, categoria = ?, precio = ?, talle = ?, color = ?, marca = ? WHERE id = ?');
      $query->execute([$nuevoNombre, $categoria, $precio, $talle, $color, $marca, $id]);
    }

    


    


    

}