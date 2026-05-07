<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';
requireRole('pharmacie');

// Connexion à la base de données
require_once __DIR__ . '/../../../backend/includes/db.php';

$db = Database::getInstance()->getConnection();
$user_id = $_SESSION['user_id'];
$message = '';
$showModal = false;

// Récupérer l'id de la pharmacie liée à ce pharmacien
$stmt = $db->prepare('SELECT id FROM pharmacies WHERE pharmacien_id = ?');
$stmt->execute([$user_id]);
$pharmacie = $stmt->fetch();
$pharmacie_id = $pharmacie['id'] ?? null;

// Gestion inscription
if (isset($_POST['btn_inscrire'])) {
    $date_garde = $_POST['date_garde'] ?? '';
    $heure_debut = $_POST['heure_debut'] ?? '';
    $heure_fin = $_POST['heure_fin'] ?? '';
    $today = date('Y-m-d');
    $showModal = true;
    
    if (!$pharmacie_id) {
        $message = '<div class="alert alert-danger">Aucune pharmacie associée à votre compte.</div>';
    } elseif (empty($date_garde) || empty($heure_debut) || empty($heure_fin)) {
        $message = '<div class="alert alert-danger">Tous les champs sont obligatoires.</div>';
    } elseif ($date_garde < $today) {
        $message = '<div class="alert alert-danger">La date de garde ne peut pas être dans le passé.</div>';
    } else {
        // Vérifier doublon
        $stmt = $db->prepare('SELECT id FROM gardes WHERE pharmacie_id = ? AND date_garde = ?');
        $stmt->execute([$pharmacie_id, $date_garde]);
        if ($stmt->fetch()) {
            $message = '<div class="alert alert-warning">Vous êtes déjà inscrit pour une garde à cette date.</div>';
        } else {
            try {
                $stmt = $db->prepare('INSERT INTO gardes (pharmacie_id, date_garde, heure_debut, heure_fin) VALUES (?, ?, ?, ?)');
                $stmt->execute([$pharmacie_id, $date_garde, $heure_debut, $heure_fin]);
                $message = '<div class="alert alert-success">Inscription pour la garde réussie !</div>';
                $showModal = false;
            } catch (Exception $e) {
                $message = '<div class="alert alert-danger">Erreur lors de l\'inscription.</div>';
            }
        }
    }
}

// Gestion annulation
if (isset($_POST['btn_annuler']) && isset($_POST['garde_id'])) {
    $garde_id = (int)$_POST['garde_id'];
    $stmt = $db->prepare('SELECT id FROM gardes WHERE id = ? AND pharmacie_id = ?');
    $stmt->execute([$garde_id, $pharmacie_id]);
    if ($stmt->fetch()) {
        $stmt = $db->prepare('DELETE FROM gardes WHERE id = ?');
        $stmt->execute([$garde_id]);
        $message = '<div class="alert alert-success">Garde annulée avec succès.</div>';
    } else {
        $message = '<div class="alert alert-danger">Impossible d\'annuler cette garde.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pharmacies de garde - Pharmacien | PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="./css/variables.css" />
  <link rel="stylesheet" href="css/dashboard-styles.css" />
</head>
<body>
  <?php include __DIR__ . '/../../includes/nav-pharmacien.php'; ?>

      <div class="dashboard-header">
        <h1 class="page-title">
          <i class="fas fa-shield-alt me-2"></i>Pharmacies de garde
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerGuardModal">
          <i class="fas fa-plus me-2"></i>S'inscrire pour garde
        </button>
      </div>

      <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle me-2"></i>
        Inscrivez-vous pour participer aux services de garde 24h/24
      </div>

      <?php if ($message) echo $message; ?>

      <div class="row mt-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Mes prochaines gardes</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <?php
                if ($pharmacie_id) {
                    $stmt = $db->prepare('SELECT id, date_garde, heure_debut, heure_fin FROM gardes WHERE pharmacie_id = ? ORDER BY date_garde ASC');
                    $stmt->execute([$pharmacie_id]);
                    $gardes = $stmt->fetchAll();
                    if ($gardes) {
                        echo '<table class="table table-hover"><thead><tr><th>Date</th><th>Heure début</th><th>Heure fin</th><th>Action</th></tr></thead><tbody>';
                        foreach ($gardes as $garde) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars(date('d/m/Y', strtotime($garde['date_garde']))) . '</td>';
                            echo '<td>' . htmlspecialchars(substr($garde['heure_debut'], 0, 5)) . '</td>';
                            echo '<td>' . htmlspecialchars(substr($garde['heure_fin'], 0, 5)) . '</td>';
                            echo '<td>';
                            echo '<form method="post" style="display:inline;">';
                            echo '<input type="hidden" name="garde_id" value="' . $garde['id'] . '" />';
                            echo '<button type="submit" name="btn_annuler" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Annuler cette garde ?\');">Annuler</button>';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<p class="text-center text-muted">Aucune garde enregistrée.</p>';
                    }
                } else {
                    echo '<p class="text-center text-danger">Aucune pharmacie liée à ce compte.</p>';
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal Inscription Garde -->
  <div class="modal fade<?php if ($showModal) echo ' show'; ?>" id="registerGuardModal" tabindex="-1"<?php if ($showModal) echo ' style="display:block;" aria-modal="true" role="dialog"'; ?>>
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">S'inscrire pour une garde</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <?php if ($showModal && $message) echo $message; ?>
            <div class="mb-3">
              <label class="form-label">Date de garde</label>
              <input type="date" class="form-control" name="date_garde" required value="<?php echo isset($_POST['date_garde']) ? htmlspecialchars($_POST['date_garde']) : ''; ?>" />
            </div>
            <div class="mb-3">
              <label class="form-label">Heure début</label>
              <input type="time" class="form-control" name="heure_debut" required value="<?php echo isset($_POST['heure_debut']) ? htmlspecialchars($_POST['heure_debut']) : ''; ?>" />
            </div>
            <div class="mb-3">
              <label class="form-label">Heure fin</label>
              <input type="time" class="form-control" name="heure_fin" required value="<?php echo isset($_POST['heure_fin']) ? htmlspecialchars($_POST['heure_fin']) : ''; ?>" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary" name="btn_inscrire">S'inscrire</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <?php if ($showModal): ?>
  <script>
    window.onload = function() {
      var myModal = new bootstrap.Modal(document.getElementById('registerGuardModal'));
      myModal.show();
    };
  </script>
  <?php endif; ?>
  <script src="js/sidebar-toggle.js"></script>
</body>
</html>
