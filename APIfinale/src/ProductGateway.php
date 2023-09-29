<?php

class ProductGateway{
    private PDO $conn;
    

    public function __construct(Database $database)
    {
        $this-> conn = $database->getConnection();
    }

    public function getAll() : array{
        $sql= "SELECT *  FROM product";

        $stmt = $this->conn->query($sql);

        $data  = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $data[] = $row;
        }

        return $data;
    }

    public function create (array $data):string
    {
        $sql = "INSERT INTO product (Marca, Model , Submodel, Transmisie, Combustibil, Km, Tip_stoc, Pret_auto_fara_TVA(EUR), Culoare_interior, Culoare_exterior, data) 
        VALUES (:Marca, :Model, :Submodel, :Transmisie, :Combustibil, :Km, :Tip_stoc, :Pret_auto_fara_TVA(EUR), :Culoare_interior, :Culoare_exterior, :data)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":Marca", $data["Marca"], PDO::PARAM_STR);
        $stmt->bindValue(":Model", $data["Model"], PDO::PARAM_STR);
        $stmt->bindValue(":Submodel", $data["Submodel"], PDO::PARAM_STR);
        $stmt->bindValue(":Transmisie", $data["Transmisie"], PDO::PARAM_STR);
        $stmt->bindValue(":Combustibil", $data["Combustibil"], PDO::PARAM_STR);
        $stmt->bindValue(":Km", $data["Km"], PDO::PARAM_INT);
        $stmt->bindValue(":Tip_stoc", $data["Tip_stoc"], PDO::PARAM_STR);
        $stmt->bindValue(":Pret_auto_fara_TVA(EUR)", $data["Pret_auto_fara_TVA(EUR)"], PDO::PARAM_INT);
        $stmt->bindValue(":Culoare_interior", $data["Culoare_interior"], PDO::PARAM_STR);
        $stmt->bindValue(":Culoare_exterior", $data["Culoare_exterior"], PDO::PARAM_STR);
        $stmt->bindValue(":data", $data["data"],strtotime(data("Y-m-d H:i:s")), PDO::PARAM_STR);

        $stmt->execute();

        return $this->conn->lastInsertId();
    } 

    public function get(string $id): array
    {
        $sql="SELECT * FROM product WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data=$stmt->fetch(PDO::FETCH_ASSOC);

        // if($data !== false) {
        //     $data["is_available"] = (bool) $data["is_available"];
        // }

        $data = (array)$data;

        return $data;
    }

    public function update(array $current, array $new): int
    {
        $sql =  "UPDATE product
                SET  Marca= :Marca, Model = :Model, Submodel = :Submodel, Transmisie = :Transmisie, Tractiune = :Tractiune,
                Combustibil = :Combustibil, Km= :Km, Tip_stoc = :Tip_stoc, Pret_auto_fara_TVA(EUR) = :Pret_auto_fara_TVA(EUR),
                Culoare_interior = :Culoare_interior, Culoare_exterior= :Culoare_exterior, data= :data
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":Marca", $new["Marca"] ?? $current["Marca"], 
        PDO::PARAM_STR);
        $stmt->bindValue(":Model", $new["Model"] ?? $current["Model"], 
        PDO::PARAM_STR);
        $stmt->bindValue(":Submodel", $new["Submodel"] ?? $current["Submodel"], 
        PDO::PARAM_STR);
        $stmt->bindValue(":Transmisie", $new["Transmisie"] ?? $current["Transmisie"], 
        PDO::PARAM_STR);
        $stmt->bindValue(":Tractiune", $new["Tractiune"] ?? $current["Tractiune"],
        PDO::PARAM_STR);
        $stmt->bindValue(":Combustibil", $new["Combustibil"] ?? $current["Combustibil"],
        PDO::PARAM_STR);
        $stmt->bindValue(":Km", $new["Km"] ?? $current["Km"], 
        PDO::PARAM_INT);
        $stmt->bindValue(":Tip_stoc", $new["Tip_stoc"] ?? $current["Tip_stoc"],
        PDO::PARAM_STR);
        $stmt->bindValue(":Pret_auto_fara_TVA(EUR)", $new["Pret_auto_fara_TVA(EUR)"] ?? $current["Pret_auto_fara_TVA(EUR)"],
        PDO::PARAM_INT);
        $stmt->bindValue(":Culoare_interior", $new["Culoare_interior"] ?? $current["Culoare_interior"],
        PDO::PARAM_STR);
        $stmt->bindValue(":Culoare_exterior", $new["Culoare_exterior"] ?? $current["Culoare_exterior"],
        PDO::PARAM_STR);
        $stmt->bindValue(":data", $new["data"] ?? $current["data"],
        PDO::PARAM_STR);

        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(string $id):intdiv{
        $sql = "DELETE FROM product
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}