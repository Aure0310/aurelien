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
                Date DESC
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
    try {
        $sql = "UPDATE Intervention SET
                ID_Client = :ID_Client,
                ID_Intervenant = :ID_Intervenant,
                Date = :Date,
                Commentaire = :Commentaire,
                ID_Type = :ID_Type,
                ID_Statut = :ID_Statut,
                ID_Urgence = :ID_Urgence
                WHERE ID = :ID";

        $stmt = $this->link->prepare($sql);

        $stmt->bindValue(':ID_Client', $data['ID_Client'], \PDO::PARAM_INT);
        $stmt->bindValue(':ID_Intervenant', $data['ID_Intervenant'], \PDO::PARAM_INT);
        $stmt->bindValue(':Date', $data['Date'], \PDO::PARAM_STR);
        $stmt->bindValue(':Commentaire', $data['Commentaire'], \PDO::PARAM_STR);
        $stmt->bindValue(':ID_Type', $data['ID_Type'], \PDO::PARAM_INT);
        $stmt->bindValue(':ID_Statut', $data['ID_Statut'], \PDO::PARAM_INT);
        $stmt->bindValue(':ID_Urgence', $data['ID_Urgence'], \PDO::PARAM_INT);
        $stmt->bindValue(':ID', $id, \PDO::PARAM_INT);

        $stmt->execute();

        return true;
    } catch (\PDOException $e) {
        return false;
    }
}

public function updateUserProfile($userId, $data)
{
    $sql = "UPDATE users SET nom = :nom, prenom = :prenom, adresse = :adresse, telephone = :telephone WHERE id = :id";
    $stmt = $this->link->prepare($sql);
    $stmt->bindValue(':nom', $data['nom'], \PDO::PARAM_STR);
    $stmt->bindValue(':prenom', $data['prenom'], \PDO::PARAM_STR);
    $stmt->bindValue(':adresse', $data['adresse'], \PDO::PARAM_STR);
    $stmt->bindValue(':telephone', $data['telephone'], \PDO::PARAM_STR);
    $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
    $stmt->execute();
}

    public function render(string $name, array $data) :string
    {
        return $this->twig->render($name, $data);
    }
}