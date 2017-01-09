<?php
class PersonnagesManager
{
  private $db; // Instance de PDO
  
  public function __construct($db)
  {
    $this->db = $db;
  }
  
  public function add(Personnage $perso)
  {
    $q = $this->db->prepare('INSERT INTO personnages_v2(nom, type, date_creation) VALUES(:nom, :type, NOW())');
    
    $q->bindValue(':nom', $perso->nom());
    $q->bindValue(':type', $perso->type());
    
    $q->execute();
    
    $perso->hydrate([
      'id' => $this->db->lastInsertId(),
      'degats' => 0,
      'atout' => 0,
      'niveau' => 0,
      'experience' => 0
    ]);
  }

  public function count()
  {
    return $this->db->query('SELECT COUNT(*) FROM personnages_v2')->fetchColumn();
  }
  
  public function delete(Personnage $perso)
  {
    $this->db->exec('DELETE FROM personnages_v2 WHERE id = '.$perso->id());
  }
  
  public function exists($info)
  {
    if (is_int($info)) // On veut voir si tel personnage ayant pour id $info existe.
    {
      return (bool) $this->db->query('SELECT COUNT(*) FROM personnages_v2 WHERE id = '.$info)->fetchColumn();
    }
    
    // Sinon, c'est qu'on veut vérifier que le nom existe ou pas.
    
    $q = $this->db->prepare('SELECT COUNT(*) FROM personnages_v2 WHERE nom = :nom');
    $q->execute([':nom' => $info]);
    
    return (bool) $q->fetchColumn();
  }
  
  public function get($info)
  {
    if (is_int($info))
    {
      $q = $this->db->query('SELECT id, nom, degats, timeEndormi, type, atout, niveau, experience, date_creation FROM personnages_v2 WHERE id = '.$info);
      $perso = $q->fetch(PDO::FETCH_ASSOC);
    }
    
    else
    {
      $q = $this->db->prepare('SELECT id, nom, degats, timeEndormi, type, atout, niveau, experience, date_creation FROM personnages_v2 WHERE nom = :nom');
      $q->execute([':nom' => $info]);
      
      $perso = $q->fetch(PDO::FETCH_ASSOC);
    }
   
    
    switch ($perso['type'])
    {
      case 'guerrier': return new Guerrier($perso);
      case 'magicien': return new Magicien($perso);
      default: return null;
    }
  }
  
  public function getList($nom)
  {
    $persos = [];
    
    $q = $this->db->prepare('SELECT id, nom, degats, timeEndormi, type, atout, niveau, experience, date_creation FROM personnages_v2 WHERE nom <> :nom ORDER BY nom');
    $q->execute([':nom' => $nom]);
    
    while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
    {
      switch ($donnees['type'])
      {
        case 'guerrier': $persos[] = new Guerrier($donnees); break;
        case 'magicien': $persos[] = new Magicien($donnees); break;
      }
    }
    return $persos;
  }
  public function subDegats()
  {
    $reponse = $this->db->query('SELECT degats FROM personnages_v2');
    
    while ($donnees = $reponse->fetch())
    {
      $donnees['degats'] -= 10;
      $q = $this->db->prepare('UPDATE personnages_v2 SET degats = :degats');    
      $q->bindValue(':degats', $donnees['degats'], PDO::PARAM_INT);
      $q->execute();
    }
  }
  
  public function update(Personnage $perso)
  {
    $q = $this->db->prepare('UPDATE personnages_v2 SET degats = :degats, timeEndormi = :timeEndormi, atout = :atout, niveau = :niveau, experience = :experience WHERE id = :id');
    
    $q->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
    $q->bindValue(':timeEndormi', $perso->timeEndormi(), PDO::PARAM_INT);
    $q->bindValue(':atout', $perso->atout(), PDO::PARAM_INT);
    $q->bindValue(':id', $perso->id(), PDO::PARAM_INT);
    $q->bindValue(':niveau', $perso->niveau(), PDO::PARAM_INT);    
    $q->bindValue(':experience', $perso->experience(), PDO::PARAM_INT);
    $q->execute();
  }
}
?>