<?php

class ProductController
{
    public function __construct(private ProductGateway $gateway)
    {
        
    }

    public function processRequest(string $method, ?string $id): void
    {
        if($id){
            $this->processResourceRequest($method, $id);
        }
        else{
            $this-> processCollectionRequest ($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void{
        $product = $this->gateway->get($id);

        if($product === [false]){
            http_response_code(404);
            echo json_encode(["message" => "Product not found"]);
            return;
        }
        switch($method)
        {
            case "GET":
                echo json_encode($product);
                break;
            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data, false);

                if(!empty($errors)) {
                    http_response_code(422); 
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                $rows = $this->gateway->update($product, $data);
                
                
                echo json_encode([
                    "message" => "Product $id updated",
                    "rows" => $rows 
                ]);
                break;
            case "DELETE":
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    "message" => "Product $id deleted",
                    "rows" => $rows
                ]);
                break;
            default :
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
        
    }
    private function processCollectionRequest (string $method): void{
        switch ($method){
            case "GET":{ 
                echo json_encode($this->gateway->getAll()); 
                break;
            }
            case "POST":{
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if(!empty($errors)) {
                    http_response_code(422); 
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                $id = $this->gateway->create($data);
                
                http_response_code(201);
                
                echo json_encode([
                    "message" => "Product created",
                    "id" => $id
                ]);
                break;
            }

            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }

    private function processLastChangedCollection ($last_date): void{
        echo json_encode($this->gateway->gatLastChanged($last_date));
    }

    private function getValidationErrors(array $data, bool $is_new = true):array
    {
        $errors = [];

        if($if_new && empty($data["Marca"])) {
            $errors[] = "Marca is required";
        }

        if(array_key_exists("Km", $data)) {
            if(filter_var($data["Km"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "Km must be an integer";
            }
        }

        if(array_key_exists("Pret_auto_fara_TVA(EUR)", $data)) {
            if(filter_var($data["Pret_auto_fara_TVA(EUR)"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "Pret_auto_fara_TVA(EUR) must be an integer";
            }
        }
        return $errors;
    }
}
