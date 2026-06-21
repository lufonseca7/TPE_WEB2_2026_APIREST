<?php
    require_once './libs/router.php';
    require_once './app/controllers/productosApiController.php';

    $router = new Router();

    #                  endpoint                      verbo      controller                   metodo
    $router->addRoute('productos',                  'GET',     'ProductosApiController',   'getProductos');
    $router->addRoute('productos/:id',              'GET',     'ProductosApiController',   'getProductoByID');
    $router->addRoute('productos/:id',              'DELETE',  'ProductosApiController',   'removeProduct');
    $router->addRoute('productos',                  'POST',    'ProductosApiController',   'addProduct');
    $router->addRoute('productos/:id',              'PUT',     'ProductosApiController',   'editProduct');
    $router->addRoute('productos/:id',              'PUT',     'ProductosApiController',   'updateProduct');
    $router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);