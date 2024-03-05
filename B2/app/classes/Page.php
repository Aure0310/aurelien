<?php

namespace App;

class Page
{
    private \Twig\Environment $twig;
    private $link;
    public $session;

    function __construct()
    {
        $this->session = new Session();
        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $this->twig = new \Twig\Environment($loader, [
            'cache' => '../var/cache/compilation_cache',
            'debug' => true,
        ]);

        try {
            $this->link = new \PDO('mysql:host=mysql;dbname=bdd-ae', "root", "");
        } catch (PDOException $e) {
            var_dump($e->getMessage());
            die();
        }
    }

    public function insert(string $table_name, array $data)
    {
        $sql = 'INSERT INTO ' . $table_name . ' (email, password, nom, prenom, adresse, telephone) VALUES (:email, :password, :nom, :prenom, :adresse, :telephone)';
        $sth = $this->link->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $sth->execute($data);
    }

    public function getUserByEmail(array $data)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $sth = $this->link->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $sth->execute($data);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function getUserById(array $data)
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $sth = $this->link->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $sth->execute($data);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM users ORDER BY nom, prenom";
        $sth = $this->link->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getUsersByRole($role)
{
    $sql = "SELECT * FROM users WHERE role = :role ORDER BY nom, prenom";
    $stmt = $this->link->prepare($sql);
    $stmt->bindParam(':role', $role);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
    
    public function getAllClients()
    {
        $sql = "SELECT * FROM users WHERE role = 'Client' ORDER BY Nom, Prenom";
        $sth = $this->link->query($sql);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllIntervenants()
    {
        $sql = "SELECT * FROM users WHERE role = 'Intervenant' ORDER BY Nom, Prenom";
        $sth = $this->link->query($sql);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllStatuts()
    {
        $sql = "SELECT * FROM Statut";
        $sth = $this->link->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllTypes()
    {
        $sql = "SELECT * FROM Type";
        $sth = $this->link->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllUrgences()
    {
        $sql = "SELECT * FROM Urgence";
        $sth = $this->link->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllRoles()
    {
        $roles = ["Admin", "Standardiste", "Intervenant", "Client"];
        return $roles;
    }

    public function getInterventionsByUser($userId)
    {
        $sql = "
            SELECT 
                Intervention.*,
                Type.Nom AS Type,
                Statut.Nom AS Statut,
                Urgence.Niveau AS Urgence
            FROM 
                Intervention
                JOIN Type ON Intervention.ID_Type = Type.ID_Type
                JOIN Statut ON Intervention.ID_Statut = Statut.ID_Statut
                JOIN Urgence ON Intervention.ID_Urgence = Urgence.ID_Urgence
            WHERE 
                ID_Client = :userId OR ID_Intervenant = :userId 
            ORDER BY 
                Date ASC
        ";
    
        $sth = $this->link->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $sth->execute(['userId' => $userId]);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function insertIntervention(array $data)
    {
        $sql = 'INSERT INTO Intervention (ID_Client, ID_Intervenant, Date, Commentaire, ID_Type, ID_Statut, ID_Urgence) VALUES (:ID_Client, :ID_Intervenant, :Date, :Commentaire, :ID_Type, :ID_Statut, :ID_Urgence)';
        $sth = $this->link->prepare($sql);
        $sth->execute($data);
    }


    public function getInterventionDetails($interventionId)
    {
        $sql = "SELECT i.*, u.Nom AS IntervenantNom, u.Prenom AS IntervenantPrenom,
                    c.Nom AS ClientNom, c.Prenom AS ClientPrenom,
                    t.Nom AS TypeNom, s.Nom AS StatutNom, urg.Niveau AS UrgenceNiveau
                FROM Intervention i
                JOIN users u ON i.ID_Intervenant = u.ID
                JOIN users c ON i.ID_Client = c.ID
                JOIN Type t ON i.ID_Type = t.ID_Type
                JOIN Statut s ON i.ID_Statut = s.ID_Statut
                JOIN Urgence urg ON i.ID_Urgence = urg.ID_Urgence
                WHERE i.ID = :interventionId";
        
        $sth = $this->link->prepare($sql);
        $sth->bindParam(':interventionId', $interventionId, \PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

public function updateIntervention($id, $data)
{
    $sql = "UPDATE Intervention SET
                    ID_Client = :ID_Client,
                    ID_Intervenant = :ID_Intervenant,
                    Date = :Date,
                    Commentaire = :Commentaire,
                    ID_Type = :ID_Type,
                    ID_Statut = :ID_Statut,
                    ID_Urgence = :ID_Urgence
                    WHERE ID = :ID";

    return $this->link->prepare($sql)->execute(array_merge($data, [':ID' => $id]));
}

public function updateUserRole($data)
{
    $sql = "UPDATE users SET role = :role WHERE id = :id";
    $this->link->prepare($sql)->execute($data);
}

public function updateUserProfile($userId, $data)
{
    $sql = "UPDATE users SET nom = :nom, prenom = :prenom, adresse = :adresse, telephone = :telephone WHERE id = :id";
    $this->link->prepare($sql)->execute(array_merge($data, [':id' => $userId]));
}

    public function render(string $name, array $data) :string
    {
        return $this->twig->render($name, $data);
    }
 

    public function insertStatut($name)
    {
        $sql = "INSERT INTO Statut (Nom) VALUES (:nom)";
        $stmt = $this->link->prepare($sql);
        $stmt->bindValue(':nom', $name);
        $stmt->execute();
    }

}