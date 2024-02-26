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


    public function render(string $name, array $data) :string
    {
        return $this->twig->render($name, $data);
    }
}