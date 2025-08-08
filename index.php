<?php
// Initialisation
$errors = [];
$data = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'telephone' => '',
    'naissance' => '',
    'genre' => '',
    'ateliers' => [],
    'participation' => '',
    'commentaires' => '',
    'conditions' => ''
];
$age = null;
$photoPath = null;

function calculAge($date)
{
    $anniv = new DateTime($date);
    $ajd = new DateTime();
    return $ajd->diff($anniv)->y;
}
// A décommenter pour tester
//var_dump($_FILES);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $key => $val) {
        if ($key === 'ateliers') {
            $data[$key] = $_POST[$key] ?? [];
        } elseif ($key !== 'conditions') {
            $data[$key] = isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
        }
    }

    // Upload photo
    $photoPath = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['photo']['tmp_name'];
        $fileName = basename($_FILES['photo']['name']);
        $fileType = mime_content_type($tmpName);

        if (str_starts_with($fileType, 'image/')) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $fileName = uniqid('photo_', true) . '_' . $fileName;
            $destination = $uploadDir . $fileName;
            move_uploaded_file($tmpName, $destination);
            $photoPath = 'uploads/' . $fileName;
        } else {
            $errors['photo'] = "Le fichier doit être une image.";
        }
    }


    // Script troll (à ne pas faire en prod car interdit)
    foreach (['nom', 'prenom', 'commentaires'] as $champ) {
        if (isset($data[$champ]) && preg_match('/<\s*script/i', $data[$champ])) {
            echo <<<HTML
            <h1>🔥 Nice try 😎</h1>
            <script>
                alert("Tu viens de te faire piéger !\\nTroll réussi.");
                document.body.innerHTML = "<h1 style='color:red'>Et voilà 😈</h1><p>Fais pas ça en vrai !</p>";
            </script>
HTML;
            exit;
        }
    }
    
    // Validation
    if (!preg_match('/^[\p{L} -]{2,50}$/u', $data['nom'])) $errors['nom'] = 'Nom invalide.';
    if (!preg_match('/^[\p{L} -]{2,50}$/u', $data['prenom'])) $errors['prénom'] = 'Prenom invalide.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email invalide.';
    if (!preg_match('/^0[6-7][0-9]{8}$/', $data['telephone'])) $errors['telephone'] = 'Téléphone invalide.';
    if (!$data['naissance']) {
        $errors['naissance'] = 'Date de naissance requise.';
    } else {
        $age = calculAge($data['naissance']);
        if ($age < 18) $errors['naissance'] = 'Vous devez avoir au moins 18 ans.';
    }
    if (!in_array($data['genre'], ['Homme', 'Femme', 'lgbtqia+'])) $errors['genre'] = 'Choix du genre requis.';
    if (empty($data['ateliers'])) $errors['ateliers'] = 'Choisissez au moins un atelier.';
    if (!in_array($data['participation'], ['Visiteur', 'Bénévole', 'Intervenant'])) $errors['participation'] = 'Type de participation invalide.';
    if (strlen($data['commentaires']) > 300) $errors['commentaires'] = 'Maximum 300 caractères.';
    if (!isset($_POST['conditions'])) $errors['conditions'] = 'Vous devez accepter le règlement.';

    // Succès
    if (empty($errors)) {
        echo "<h2>Inscription réussie !</h2><ul>";
        foreach ($data as $key => $value) {
            if ($key === 'ateliers') $value = implode(', ', array_map('htmlspecialchars', $value));
            if ($key === 'commentaires') $value = nl2br(htmlspecialchars($value));
            if ($key === 'naissance') $value = htmlspecialchars($value) . " (Âge : $age ans)";
            if ($key !== 'conditions') {
                echo "<li><strong>" . mb_convert_case($key, MB_CASE_TITLE, "UTF-8") . " :</strong> $value</li>";

            }
        }
        if ($photoPath) {
            echo "<li><strong>Photo :</strong><br>";
            echo "<img src=\"$photoPath\" alt=\"Photo envoyée\" style=\"max-width:200px; border:1px solid #ccc;\">";
            echo "<br><a href=\"$photoPath\" target=\"_blank\">Voir / Télécharger</a></li>";
        }

        echo "</ul>";
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
        }

        .error {
            color: red;
            font-style: italic;
            margin: 0 0 10px 0;
        }

        .flex-wrap {
            display: flex;
            align-items: flex-start;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap; /* <-- ajout important */
        }

        .col-left {
            flex: 1;
            min-width: 300px;
        }

        .col-photo {
            width: 250px;
            flex-shrink: 0;
        }

        .photo-upload {
            width: 250px;
            height: 250px;
            border: 2px dashed #aaa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 50px;
            cursor: pointer;
        }

        .photo-upload:hover {
            border-color: #333;
            color: #333;
        }

        #photo {
            display: none;
        }

        .ligne-formulaire {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .libelle {
            font-weight: bold;
            width: 150px;
        }

        .choix-radios {
            display: flex;
            gap: 15px;
            align-items: center;
        }
    </style>
</head>
<body>
<form method="post" enctype="multipart/form-data">
    <div class="col-photo">
        <label for="photo">Photo (facultatif) :</label>
        <label for="photo" class="photo-upload">+</label>
        <input type="file" name="photo" id="photo" accept="image/*">
        <?php if (isset($errors['photo'])) echo "<p class='error'>{$errors['photo']}</p>"; ?>
    </div>
    <div class="flex-wrap">
        <div class="col-left">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($data['nom']) ?>">
            <?php if (isset($errors['nom'])) echo "<p class='error'>{$errors['nom']}</p>"; ?><br><br>

            <label for="prénom">Prénom :</label>
            <input type="text" name="prenom" id="prénom" value="<?= htmlspecialchars($data['prenom']) ?>">
            <?php if (isset($errors['prenom'])) echo "<p class='error'>{$errors['prenom']}</p>"; ?><br><br>

            <label for="email">Email :</label>
            <input type="text" name="email" id="email" value="<?= htmlspecialchars($data['email']) ?>">
            <?php if (isset($errors['email'])) echo "<p class='error'>{$errors['email']}</p>"; ?><br><br>

            <label for="telephone">Téléphone :</label>
            <input type="text" name="telephone" id="telephone" value="<?= htmlspecialchars($data['telephone']) ?>">
            <?php if (isset($errors['telephone'])) echo "<p class='error'>{$errors['telephone']}</p>"; ?><br><br>

            <label for="naissance">Date de naissance :</label>
            <input type="date" name="naissance" id="naissance" value="<?= htmlspecialchars($data['naissance']) ?>">
            <?php if (isset($errors['naissance'])) echo "<p class='error'>{$errors['naissance']}</p>"; ?><br><br>
        </div>


    </div>

    <div class="ligne-formulaire">
        <span class="libelle">Genre :</span>
        <div class="choix-radios">
            <label><input type="radio" name="genre" value="Homme" <?= $data['genre'] === 'Homme' ? 'checked' : '' ?>>
                Homme</label>
            <label><input type="radio" name="genre" value="Femme" <?= $data['genre'] === 'Femme' ? 'checked' : '' ?>>
                Femme</label>
            <label><input type="radio" name="genre"
                          value="lgbtqia+" <?= $data['genre'] === 'lgbtqia+' ? 'checked' : '' ?>> lgbtqia+</label>
        </div>
    </div>
    <?php if (isset($errors['genre'])) echo "<p class='error'>{$errors['genre']}</p>"; ?><br>

    <div class="ligne-formulaire">
        <span class="libelle">Ateliers :</span>
        <div class="choix-radios">
            <label><input type="checkbox" name="ateliers[]"
                          value="Barbe à papa au chocolat" <?= in_array('Barbe à papa au chocolat', $data['ateliers']) ? 'checked' : '' ?>>
                Barbe à papa au chocolat</label>
            <label><input type="checkbox" name="ateliers[]"
                          value="Bien-être façon Rocco" <?= in_array('Bien-être façon Rocco', $data['ateliers']) ? 'checked' : '' ?>>
                Bien-être façon Rocco</label>
            <label><input type="checkbox" name="ateliers[]"
                          value="Informatique pour les nuls" <?= in_array('Informatique pour les nuls', $data['ateliers']) ? 'checked' : '' ?>>
                Informatique pour les nuls</label>
        </div>
    </div>
    <?php if (isset($errors['ateliers'])) echo "<p class='error'>{$errors['ateliers']}</p>"; ?><br>

    <label for="participation">Type de participation :</label><br>
    <select name="participation" id="participation">
        <option value="">-- Choisissez --</option>
        <option value="Visiteur" <?= $data['participation'] === 'Visiteur' ? 'selected' : '' ?>>Visiteur</option>
        <option value="Bénévole" <?= $data['participation'] === 'Bénévole' ? 'selected' : '' ?>>Bénévole</option>
        <option value="Intervenant" <?= $data['participation'] === 'Intervenant' ? 'selected' : '' ?>>Intervenant
        </option>
    </select>
    <?php if (isset($errors['participation'])) echo "<p class='error'>{$errors['participation']}</p>"; ?><br>

    <label for="commentaires">Commentaires (optionnel) :</label><br>
    <textarea name="commentaires" id="commentaires" maxlength="300"
              rows="5"><?= htmlspecialchars($data['commentaires']) ?></textarea><br>
    <?php if (isset($errors['commentaires'])) echo "<p class='error'>{$errors['commentaires']}</p>"; ?><br>

    <input type="checkbox" name="conditions" id="conditions" value="1">
    <label for="conditions">
        J’accepte le <a href="reglement.html" target="_blank">règlement de l’événement</a>

    </label>

    <?php if (isset($errors['conditions'])) echo "<p class='error'>{$errors['conditions']}</p>"; ?><br>

    <br><br>
    <button type="submit">Valider</button>
</form>
</body>
</html>
