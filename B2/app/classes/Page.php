<?php

namespace App;

class Page
{
    private \Twig\Environment $twig;
    private $link;
    public $session;

    function __construct()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

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

public function getInterventionsByUser($userId, $filter_intervenant = '')
{
    if ($this->session->hasRole('Admin') || $this->session->hasRole('Standardiste')) {
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
                1
        ";

         if (!empty($filter_intervenant)) {
            $sql .= " AND ID_Intervenant = :filter_intervenant";
        }

        $sql .= " ORDER BY Date ASC";

        $sth = $this->link->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);

         $params = [];
        if (!empty($filter_intervenant)) {
            $params[':filter_intervenant'] = $filter_intervenant;
        }

        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    } else {
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
                ID_Client = :userId OR ID_Intervenant = :userId OR ID_Intervenant2 = :userId
            ORDER BY 
                Date ASC
        ";

        $sth = $this->link->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $sth->execute(['userId' => $userId]);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
}


    public function insertIntervention(array $data)
    {
        $sql = 'INSERT INTO Intervention (ID_Client, ID_Intervenant, ID_Intervenant2, Date, Commentaire, ID_Type, ID_Statut, ID_Urgence) VALUES (:ID_Client, :ID_Intervenant, :ID_Intervenant2, :Date, :Commentaire, :ID_Type, :ID_Statut, :ID_Urgence)';
        $sth = $this->link->prepare($sql);
        $sth->execute($data);
    }


    public function getInterventionDetails($interventionId)
    {
        $sql = "SELECT i.*, u.Nom AS IntervenantNom, u.Prenom AS IntervenantPrenom,
                    u2.Nom AS Intervenant2Nom, u2.Prenom AS Intervenant2Prenom,
                    c.Nom AS ClientNom, c.Prenom AS ClientPrenom,
                    t.Nom AS TypeNom, s.Nom AS StatutNom, urg.Niveau AS UrgenceNiveau
                FROM Intervention i
                JOIN users u ON i.ID_Intervenant = u.ID
                LEFT JOIN users u2 ON i.ID_Intervenant2 = u2.ID
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
                    ID_Intervenant2 = :ID_Intervenant2,
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

public function addStatut($nom)
{
    $sql = "INSERT INTO Statut (Nom) VALUES (:nom)";
    $sth = $this->link->prepare($sql);
    return $sth->execute(['nom' => $nom]);
}

public function addType($nom)
{
    $sql = "INSERT INTO Type (Nom) VALUES (:nom)";
    $sth = $this->link->prepare($sql);
    return $sth->execute(['nom' => $nom]);
}

public function addUrgence($niveau)
{
    $sql = "INSERT INTO Urgence (Niveau) VALUES (:niveau)";
    $sth = $this->link->prepare($sql);
    return $sth->execute(['niveau' => $niveau]);
}

public function getStatutById($statutId)
{
    $sql = "SELECT * FROM Statut WHERE ID_Statut = :statutId";
    $stmt = $this->link->prepare($sql);
    $stmt->execute(['statutId' => $statutId]);

    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

public function getTypeById($typeId)
{
    $sql = "SELECT * FROM Type WHERE ID_Type = :typeId";
    $stmt = $this->link->prepare($sql);
    $stmt->execute(['typeId' => $typeId]);

    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

public function getUrgenceById($urgenceId)
{
    $sql = "SELECT * FROM Urgence WHERE ID_Urgence = :urgenceId";
    $stmt = $this->link->prepare($sql);
    $stmt->execute(['urgenceId' => $urgenceId]);

    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

public function editStatut($id, $data)
{
    try {
        $sql = "UPDATE Statut SET Nom = :nom WHERE ID_Statut = :id";
        $stmt = $this->link->prepare($sql);
        $stmt->bindValue(':nom', $data['nom'], \PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return true;
    } catch (\PDOException $e) {
        return false;
    }
}


public function editType($id, $data)
{
    try {
        $sql = "UPDATE Type SET Nom = :nom WHERE ID_Type = :id";
        $stmt = $this->link->prepare($sql);
        $stmt->bindValue(':nom', $data['nom'], \PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return true;
    } catch (\PDOException $e) {
        return false;
    }
}

public function editUrgence($id, $data)
{
        try {
        $sql = "UPDATE Urgence SET Niveau = :niveau WHERE ID_Urgence = :id";
        $stmt = $this->link->prepare($sql);
        $stmt->bindValue(':niveau', $data['niveau'], \PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return true;
    } catch (\PDOException $e) {
        return false;
}
}

public function deleteStatut($id)
{
    try {
        $sql = "DELETE FROM Statut WHERE ID_Statut = :id";
        $sth = $this->link->prepare($sql);
        $sth->execute(['id' => $id]);
        return true;
    } catch (\PDOException $e) {
        return false;
    }
}

public function deleteType($id)
{
    $sql = "DELETE FROM Type WHERE ID_Type = :id";
    $sth = $this->link->prepare($sql);
    $sth->execute(['id' => $id]);
}

public function deleteUrgence($id)
{
    $sql = "DELETE FROM Urgence WHERE ID_Urgence = :id";
    $sth = $this->link->prepare($sql);
    $sth->execute(['id' => $id]);
}
 
public function deleteUsers($id)
{
    try {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->link->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        echo "Erreur" . $e->getMessage();
        return false;
    }
}

public function deleteIntervention($intervention_id)
{
    try {
        $sql = "DELETE FROM Intervention WHERE ID = :intervention_id";

        $stmt = $this->link->prepare($sql);
        $stmt->bindParam(':intervention_id', $intervention_id, \PDO::PARAM_INT);
        $stmt->execute();
        
    } catch (PDOException $e) {
        echo "Erreur" . $e->getMessage();
        return false;
    }
}

    public function countInterventionsEnCoursByIntervenant($intervenantId)
    {
        $sql = "SELECT COUNT(*) FROM Intervention WHERE (ID_Intervenant = :intervenantId OR ID_Intervenant2 = :intervenantId) AND ID_Statut != 4";
        $sth = $this->link->prepare($sql);
        $sth->bindParam(':intervenantId', $intervenantId, \PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchColumn();
    }
    
    public function countAllInterventionsEnCours()
    {
        $sql = "SELECT COUNT(*) FROM Intervention WHERE ID_Statut != 4";
        $sth = $this->link->query($sql);
        return $sth->fetchColumn();
    }
    

    public function insertStatut($name)
    {
        $sql = "INSERT INTO Statut (Nom) VALUES (:nom)";
        $stmt = $this->link->prepare($sql);
        $stmt->bindValue(':nom', $name);
        $stmt->execute();
    }

    public function searchInterventionById($interventionId)
{
    $sql = "SELECT * FROM Intervention WHERE ID = :interventionId";
    $stmt = $this->link->prepare($sql);
    $stmt->execute(['interventionId' => $interventionId]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

public function searchUserById(int $userId)
{
    $sql = "SELECT * FROM users WHERE id = :userId";
    $stmt = $this->link->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

public function render(string $name, array $data) :string
{
    return $this->twig->render($name, $data);
}

}