# TP Formulaire PHP

## 📌 Description
Ce projet est un **TP PHP** consistant à créer un formulaire d’inscription avancé **sans base de données**, avec validation complète des champs, gestion des erreurs et affichage d’un récapitulatif.

Il a été développé avec **PHP 8.3** dans **PhpStorm**.

---

## 🎯 Objectifs du TP
- Créer un formulaire HTML structuré avec mise en page personnalisée.
- Valider dynamiquement les champs en PHP.
- Pré-remplir les champs en cas d’erreur (sauf la case à cocher `Conditions`).
- Afficher un message d’erreur spécifique pour chaque champ invalide.
- Afficher un récapitulatif complet avec l’âge calculé si le formulaire est valide.
- Intégrer un upload de photo avec aperçu avant envoi.

---

## 🛠️ Technologies utilisées
- **PHP 8.3**
- **HTML5 / CSS3**
- **PhpStorm** (IDE)
- **WampServer** (serveur local)

---

## 📂 Structure du projet
tp_formulaire/
│
├── index.php # Page principale avec le formulaire
├── style.css # Styles du formulaire
├── uploads/ # Dossier pour stocker les photos uploadées
└── README.md # Documentation du projet


---

## 📋 Fonctionnalités
✅ Formulaire avec champs :  
- Nom  
- Prénom  
- Email  
- Date de naissance (calcul de l’âge)  
- Sexe (radio)  
- Ateliers (checkbox multiples)  
- Upload de photo (avec croix cliquable pour sélectionner l’image)  
- Conditions générales (case obligatoire)  

✅ Validation côté serveur :  
- Champs obligatoires non vides  
- Format email valide  
- Date cohérente pour l’âge  
- Vérification de l’extension et taille du fichier image  

✅ Affichage :  
- Erreurs en rouge sous chaque champ  
- Conservation des valeurs saisies après erreur  
- Récapitulatif clair si succès

---

## 🚀 Installation et utilisation
1. **Cloner le dépôt** :
   ```bash
   git clone https://github.com/TON-USER/tp_formulaire.git
2. Placer le dossier dans le répertoire www ou htdocs de votre serveur local.

3. Lancer WampServer ou XAMPP.

4. Accéder au projet depuis votre navigateur :
   http://localhost/tp_formulaire

   👨‍💻 Auteur

    Nom : Gabriel

    Formation : ENI - D2WM

    Date : Août 2025
