<?php
class County {
 
    private $conn;
    private $table_name = "COUNTY";
 
    public $id;
    public $name;
	
	public $address;
	public $zipCode;
	public $phone;
	public $fax;
	public $email;
	public $web;
	public $governor;
	public $viceGovernor;
	public $viceGovernor2;
	public $viceMinority;
	public $representativeBodyPresident;

    public function __construct($db) {
        $this->conn = $db;
    }
	
	function read() {
	 
		$query = "SELECT
					c.id, c.name
				FROM
					" . $this->table_name . " c
				ORDER BY
					c.id ASC";
	 
		$stmt = $this->conn->prepare($query);
	 
		$stmt->execute();
	 
		return $stmt;
	}
	
	function readOne() {
		
		$query = "SELECT
					t.id, t.name, c.address, c.zip_code, c.phone, c.fax, c.email, c.web, c.governor, c.vice, c.vice_2, c.vice_minorities, c.president_representative_body
				FROM
					" . $this->table_name . " t
					LEFT JOIN
						CONTACT c
							ON t.contact_fk = c.id
				WHERE
					t.id = ?
				LIMIT
					0,1";
	 
		$stmt = $this->conn->prepare($query);
	 
		$stmt->bindParam(1, $this->id);
	 
		$stmt->execute();
	 
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$this->name = $row['name'];
		$this->address = $row['address'];
		$this->zipCode = $row['zip_code'];
		$this->phone = $row['phone'];
		$this->fax = $row['fax'];
		$this->email = $row['email'];
		$this->web = $row['web'];
		$this->governor = $row['governor'];
		$this->viceGovernor = $row['vice'];
		$this->viceGovernor2 = $row['vice_2'];	
		$this->viceMinority = $row['vice_minorities'];
		$this->representativeBodyPresident = $row['president_representative_body'];
	}
}
