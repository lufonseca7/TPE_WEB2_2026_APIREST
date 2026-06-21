<?php

require_once './app/models/productosApiModel.php';
require_once './app/views/apiView.php';


class productosApiController{

    private $model;
    private $view;

    function __construct(){
        $this->model = new productosApiController();
        $this->view = new apiView();
       ;
    }


    public function addProduct($req, $res){
        
        $producto = $req->body->producto;
        $categoria = $req->body->categoria;
        $precio = $req->body->precio;    
        $talle = $req->body->talle;
        $color = $req->body->color;
        $marca = $req->body->marca;


        if (!empty($producto) && !empty($categoria) && !empty($precio) && !empty($talle) && !empty($color) && !empty($marca)) {
                // todos los campos tienen valor → insertar
                $id = $this->model->insertProduct($producto, $categoria, $precio, $talle, $color, $marca);
                if (!$id) {
                    return $res->json("No se pudo insertar el producto", 500);
                } 
                return $res->json("El producto fue agregado con éxito", 201);
            } else {
            return $res->json("Faltan datos obligatorios", 400);
            }


    }

    public function getProductos($req, $res){
            if (isset($req->query->orden) && isset($req->query->atributo)){
                $orden = $req->query->orden;
                $atributo = $req->query->atributo;
                $productos = $this->getByOrder($orden,$atributo);
                    return $res->json($productos,200);
            }
            else {
                $series = $this->model->getProducts();
                return $res->json($series,200);
            }
        }
    
    private function getByOrder($orden,$atributo){
        $productos = $this->model->getProducts();

        if ($orden == 'asc') {                    
                $order = SORT_ASC;
            }
            else if ($orden == 'dsc'){
                $order = SORT_DESC;
            }
         if ($atributo == 'producto' || $atributo == 'categoria' || $atributo == 'precio' || $atributo == 'talle' || $atributo == 'color' || $atributo == 'marca'){
          $aux = array_column($productos, $atributo);
          array_multisort($aux, $order, $productos);
          return $productos;
        }
        else {
            return null;
        }
    }



    public function removeProduct($req, $res){
        $id = $req->params->id;

        $product = $this->model->showProduct($id);

        if (!$product) {
            return $this->view->response("No existe el producto con el id=$id", 404);
        }
        $this->model->removeProduct($id);
        $this->view->response("El producto con el id=$id se elimino con exito", 200);
        //header('Location: ' . BASE_URL);
    }

    public function getProductoByID($req, $res){
            $id = $req->params->id;

            $producto = $this->model->showProduct($id);

            if (!$producto){
                return $this->view->response("Ups! El producto que buscas no existe ): ", 404);
            }

            return $res->json($producto,200);
        }

    public function editProduct($req, $res){
        $id = $req->params->id;
        $producto = $this->model->showProduct($id);
        
        if (!$producto){
            return $res->json("no existe la producto $producto",404);
        }
        
        if(empty($req->body->producto) || empty($req->body->categoria) || empty($req->body->precio) || empty($req->body->talle) || empty($req->body->color) || empty($req->body->marca)){
            return $res->json("hay algun/nos parametro/s vacio/s",404);
        }
        
        $producto = $req->body->producto;
        $categoria = $req->body->categoria;
        $precio = $req->body->precio;
        $talle = $req->body->talle;
        $color = $req->body->color;
        $marca = $req->body->marca;
        

        $this->model->updateProduct($id,$producto,$categoria,$precio,$talle,$color,$marca);
        $productoActualizado = $this->model->showProduct($id);
        return $res->json($productoActualizado,201);
    }

    public function updateProduct($req, $res) {

        $id = $req->params->id;

        $producto = $this->model->showProduct($id);

        if (!$producto) {
            return $this->view->response("El producto solicitado no existe", 404);
        }

        $body = $req->body;

        if (empty($body->nombre) || empty($body->categoria) || empty($body->precio) || empty($body->talle) || empty($body->color) || empty($body->marca)
        ) {
            return $this->view->response("Complete todos los campos", 400);
        }

        $this->model->updateProduct($id,$body->nombre, $body->categoria, $body->precio, $body->talle, $body->color, $body->marca);

        return $this->view->response("Producto actualizado correctamente", 200);
    }

    

}