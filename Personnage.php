<?php
abstract class Personnage
{
  protected $atout,
            $degats,
            $id,
            $nom,
            $timeEndormi,
            $type,
            $niveau,
            $experience,
            $dodo,
            $count,
            $date_creation;

  
  const CEST_MOI = 1; // Constante renvoyée par la méthode `frapper` si on se frappe soit-même.
  const PERSONNAGE_TUE = 2; // Constante renvoyée par la méthode `frapper` si on a tué le personnage en le frappant.
  const PERSONNAGE_FRAPPE = 3; // Constante renvoyée par la méthode `frapper` si on a bien frappé le personnage.
  const PERSONNAGE_ENSORCELE = 4; // Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on a bien ensorcelé un personnage.
  const PAS_DE_MAGIE = 5; // Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on veut jeter un sort alors que la magie du magicien est à 0.
  const PERSO_ENDORMI = 6; // Constante renvoyée par la méthode `frapper` si le personnage qui veut frapper est endormi.
  const PERSO_TUE_APRES_ATTAK = 7; // Constante renvoyée par la méthode `frapper` retour du baton.
  const TROIS_COUPS = 8; // Constante renvoyée par la méthode `frapper` retour du baton.
  
  public function __construct(array $donnees)
  {
    $this->hydrate($donnees);
    $this->type = strtolower(static::class);
  }
  
  public function estEndormi()
  {
    return $this->timeEndormi > time();
  }
  
  public function frapper(Personnage $perso)
  {
    $count = 0;
    if ($perso->id == $this->id)
    {
      return self::CEST_MOI;
    }
    
    elseif ($this->estEndormi() || $this->estDodo())
    {
      return self::PERSO_ENDORMI;
    }
    elseif ($count == 3)
    {
      $count = 0;
      return self::TROIS_COUPS;
    }
    else
    {
    $count += 1;
    return $perso->recevoirDegats();
    }
   // return $persoAFrape->retourduBaton();
    
    // On indique au personnage qu'il doit recevoir des dégâts.
    // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE.
    
  }
   public function dodo(Personnage $perso)
  {
    return $dodo = time()+3600;
  }
  public function estDodo()
  {
    return $this->dodo > time();
  }
  public function hydrate(array $donnees)
  {
    foreach ($donnees as $key => $value)
    {
      $method = 'set'.ucfirst($key);
      
      if (method_exists($this, $method))
      {
        $this->$method($value);
      }
    }
  }
  
  public function nomValide()
  {
    return !empty($this->nom);
  }
  
  public function recevoirDegats()
  {
    $this->experience += 5;

    if ($this->degats >= 100)
    {
      return self::PERSONNAGE_TUE;
    }
    elseif ($this->experience >= 100)
    {
      $this->experience = 0;
    }
    elseif ($this->niveau == 0)
    {
      $this->degats += 25;
    }
    elseif ($this->niveau == 1)
    {
     $this->degats += 15;
    }
    elseif ($this->niveau == 2)
    {
      $this->degats += 10;
    }
    elseif ($this->niveau == 3)
    {
      $this->degats += 5;
    }
    // Si on a 100 de dégâts ou plus, on supprime le personnage de la BDD.
  
    elseif ($this->niveau <= 2)
    {
      $this->niveau += 1;
    }
    // Sinon, on se contente de mettre à jour les dégâts du personnage.

    return self::PERSONNAGE_FRAPPE;
  }
  
  public function retourduBaton()
  {
    $this->experience += 25;

    if ($this->niveau == 0)
    {
      $this->degats += 15;
    }
    elseif ($this->niveau == 1)
    {
     $this->degats += 10;
    }
    elseif ($this->niveau == 2)
    {
      $this->degats += 5;
    }
    elseif ($this->niveau == 3)
    {
      $this->degats += 3;
    }
    // Si on a 100 de dégâts ou plus, on supprime le personnage de la BDD.
    if ($this->degats >= 100)
    {
      return self::PERSO_TUE_APRES_ATTAK;
    }
    if ($this->experience >= 100 && $this->niveau <= 2)
    {
      $this->experience == 0;
      $this->niveau += 1;
    }
    // Sinon, on se contente de mettre à jour les dégâts du personnage.

    return self::PERSONNAGE_FRAPPE;
  }

  /**
   * @return string
   */
  public function reveil()
  {
    $secondes = $this->timeEndormi;
    $secondes -=time();
    
    $heures = floor($secondes / 3600);
    $secondes -= $heures * 3600;
    $minutes = floor($secondes / 60);
    $secondes -= $minutes * 60;
    
    $heures .= $heures <= 1 ? ' heure' : ' heures';
    $minutes .= $minutes <= 1 ? ' minute' : ' minutes';
    $secondes .= $secondes <= 1 ? ' seconde' : ' secondes';
    
    return $heures . ', ' . $minutes . ' et ' . $secondes;
  }
  
  public function atout()
  {
    return $this->atout;
  }
  
  public function degats()
  {
    return $this->degats;
  }
  
  public function id()
  {
    return $this->id;
  }
  
  public function nom()
  {
    return $this->nom;
  }
  
  public function timeEndormi()
  {
    return $this->timeEndormi;
  }
  public function timeDodo()
  {
    return $this->timeDodo;
  }
  
  public function type()
  {
    return $this->type;
  }
  public function niveau()
  {
    return $this->niveau;
  }
  public function experience()
  {
    return $this->experience;
  }
   public function date_creation()
  {
    return $this->date_creation;
  }

  public function setAtout($atout)
  {
    $atout = (int) $atout;
    
    if ($atout >= 0 && $atout <= 100)
    {
      $this->atout = $atout;
    }
  }
  
  public function setDegats($degats)
  {
    $degats = (int) $degats;
    
    if ($degats >= 0 && $degats <= 100)
    {
      $this->degats = $degats;
    }
  }
  
  public function setId($id)
  {
    $id = (int) $id;
    
    if ($id > 0)
    {
      $this->id = $id;
    }
  }
  
  public function setNom($nom)
  {
    if (is_string($nom))
    {
      $this->nom = $nom;
    }
  }
  
  public function setTimeEndormi($time)
  {
    $this->timeEndormi = (int) $time;
  }
  public function setTimeDodo($time)
  {
    $this->timeDodo = (int) $time;
  }

// public function setNiveau
public function setNiveau($niveau)
  {
    $niveau = (int) $niveau;
    
    if ($niveau >= 0 && $niveau <= 3)
    {
      $this->niveau = $niveau;
    }
  }
public function setExperience($experience)
  {
    $experience = (int) $experience;
    
    if ($experience >= 0 && $experience <= 100)
    {
      $this->experience = $experience;
    }
  }
  public function setDate_creation($date_creation)
  {
      $this->date_creation = $date_creation;  
  }
}
?>

