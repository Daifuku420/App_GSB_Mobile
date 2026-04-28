# GSB Appli-CR Mobile — Guide d'utilisation

> Application Android de gestion des comptes-rendus de visite médicale — Galaxy Swiss Bourdin  
> **Dépôt GitHub :** [github.com/Daifuku420/App_GSB_Mobile](https://github.com/Daifuku420/App_GSB_Mobile)

---

## Sommaire

1. [Prérequis](#1-prérequis)
2. [Installation de l'application](#2-installation-de-lapplication)
3. [Comptes de test](#3-comptes-de-test)
4. [Utilisation de l'application](#4-utilisation-de-lapplication)
5. [Tester les API REST](#5-tester-les-api-rest)

---

## 1. Prérequis

- Android **7.0 (Nougat)** minimum — SDK 24
- Connexion internet obligatoire (pas de mode hors-ligne)
- **ThunderClient** (extension VS Code) ou **Postman** pour tester les API

---

## 2. Installation de l'application

### Via Android Studio (recommandé)

```bash
# 1. Cloner le dépôt
git clone https://github.com/Daifuku420/App_GSB_Mobile.git

# 2. Ouvrir dans Android Studio
# File → Open → sélectionner le dossier cloné

# 3. Lancer sur émulateur ou appareil physique
# Run → Run 'app'  (Shift+F10)
```

### Via APK directement

1. Télécharger l'APK depuis la page [Releases](https://github.com/Daifuku420/App_GSB_Mobile/releases) du dépôt
2. Autoriser l'installation depuis des sources inconnues sur l'appareil
3. Ouvrir le fichier `.apk` et suivre l'installation

---

## 3. Comptes de test

| Rôle | Identifiant | Mot de passe |
|---|---|---|
| Visiteur médical | `084795A` | `Test$*Av!6` |
| Délégué régional | `157246R` | `To^$s78%eH` |
| Responsable de secteur | `967438Y` | `P&é$ù22/*&` |

> Les mots de passe respectent la politique de complexité : min. 8 caractères, majuscule, chiffre et caractère spécial.

---

## 4. Utilisation de l'application

### Se connecter

1. Lancer l'application GSB
2. Saisir votre **matricule** (ex : `084795A`)
3. Saisir votre **mot de passe**
4. Appuyer sur **SE CONNECTER**

> En cas d'identifiants incorrects, un toast "Identifiants incorrects" s'affiche.

---

### Naviguer dans l'application

Le menu latéral (icône ☰ en haut à gauche) donne accès à toutes les fonctionnalités selon votre rôle :

| Entrée | Visiteur | Délégué | Responsable |
|---|:---:|:---:|:---:|
| Accueil (tableau de bord) | ✅ | ✅ | ✅ |
| Praticiens et Médicaments | ✅ | ✅ | ✅ |
| Mes comptes-rendus | ✅ | ✅ | ✅ |
| Créer un compte-rendu | ✅ | ✅ | ✅ |
| Comptes-rendus de ma région | ❌ | ✅ | ✅ |
| Tous les comptes-rendus | ❌ | ❌ | ✅ |
| Statistiques globales | ❌ | ❌ | ✅ |
| Création utilisateur | ❌ | ❌ | ✅ |
| Modification utilisateur | ❌ | ❌ | ✅ |

---

### Saisir un compte-rendu

1. Menu → **Créer un compte-rendu**
2. Renseigner les champs obligatoires (marqués d'un `*`) :
   - **Date de visite** — format `AAAA-MM-JJ`
   - **Praticien** — sélectionner dans la liste déroulante
   - **Motif** — ex : Nouveauté, Visite annuelle…
   - **Médicament présenté** — sélectionner dans la liste déroulante
   - **Description / Bilan** — texte libre
3. Cocher **Donner un échantillon** si applicable
4. Appuyer sur **ENREGISTRER LE COMPTE-RENDU**

> Un toast "Compte-rendu enregistré !" confirme la sauvegarde et redirige vers la liste.

---

### Consulter l'historique

1. Menu → **Mes comptes-rendus**
2. La liste affiche tous vos CR (date + praticien)
3. Appuyer sur un élément pour voir le **détail complet**
4. Bouton **Retour** pour revenir à la liste

---

### Se déconnecter

Menu → **Mon Profil** → bouton **SE DÉCONNECTER**  
La session est effacée et l'application revient à l'écran de connexion.

---

## 5. Tester les API REST

**Base URL :** `https://paul-padovani-thomas.com/api/`

Tous les endpoints retournent du **JSON**. Les requêtes POST envoient un corps JSON avec le header `Content-Type: application/json`.

---

### `POST` login.php — Authentification

Vérifie les identifiants et retourne les informations de l'utilisateur.

**Requête**
```json
{
  "matricule": "084795A",
  "password": "Test$*Av!6"
}
```

**Réponse 200 — Succès**
```json
{
  "status": 200,
  "data": {
    "matricule": "084795A",
    "nom": "Smith",
    "prenom": "John",
    "role": "visiteur",
    "region": "AL"
  }
}
```

**Réponse 401 — Identifiants incorrects**
```json
{
  "status": 401,
  "message": "Login failed"
}
```

---

### `POST` get_rap.php — Comptes-rendus d'un visiteur

Retourne tous les comptes-rendus du visiteur identifié par son matricule.

**Requête**
```json
{
  "matricule": "084795A"
}
```

**Réponse 200 — Succès**
```json
{
  "status": 200,
  "data": {
    "rapports": [
      {
        "rap_num": "1",
        "rap_date": "2026-01-08",
        "pra_nom": "House",
        "pra_prenom": "Gregory",
        "rap_motif": "periodicite",
        "rap_bilan": "..."
      }
    ]
  }
}
```

**Réponse 400 — Matricule manquant**
```json
{
  "status": 400,
  "message": "Missing matricule"
}
```

---

### `GET` listes.php — Praticiens et médicaments

Retourne la liste de tous les praticiens et médicaments disponibles. **Aucun corps de requête nécessaire.**

**Requête**
```
GET https://paul-padovani-thomas.com/api/listes.php
```

**Réponse 200 — Succès**
```json
{
  "status": 200,
  "data": {
    "praticiens": [
      { "pra_num": 1, "pra_nom": "House", "pra_prenom": "Gregory" }
    ],
    "medicaments": [
      { "med_depotlegal": "ADV400", "med_nomcommercial": "Advil 400" }
    ]
  }
}
```

---

### `GET` get_cr.php — Tous les comptes-rendus

Retourne l'ensemble des comptes-rendus toutes régions confondues. **Aucun corps de requête nécessaire.** Réservé au rôle Responsable de secteur.

**Requête**
```
GET https://paul-padovani-thomas.com/api/get_cr.php
```

**Réponse 200 — Succès**
```json
{
  "status": 200,
  "data": {
    "comptes_rendus": [
      {
        "rap_num": "1",
        "rap_date": "2026-01-08",
        "vis_matricule": "084795A",
        "pra_nom": "House",
        "rap_motif": "periodicite"
      }
    ]
  }
}
```

---

### `POST` insert_cr.php — Créer un compte-rendu

Insère un nouveau compte-rendu en base. Les clés étrangères (praticien, médicament, visiteur) sont vérifiées avant insertion. L'ensemble des écritures est encapsulé dans une transaction SQL.

**Requête**
```json
{
  "matricule": "084795A",
  "pra_num": 1,
  "rap_date": "2026-01-22",
  "rap_motif": "Nouveauté",
  "rap_bilan": "Bilan de la visite depuis l'application mobile. Le praticien a montré un grand intérêt.",
  "echantillons": [
    {
      "med_depotlegal": "SPASFON",
      "quantite": 2
    },
    {
      "med_depotlegal": "AMOX1G",
      "quantite": 1
    }
  ]
}
```

**Réponse 201 — Créé avec succès**
```json
{
  "status": 201,
  "message": "Visit report created successfully.",
  "rap_num": "27"
}
```

**Réponse 404 — Matricule visiteur inexistant**
```json
{
  "status": 404,
  "message": "Error: The provided visitor matricule does not exist."
}
```

**Réponse 400 — Champs obligatoires manquants**
```json
{
  "status": 400,
  "message": "Missing required fields"
}
```

**Réponse 405 — Méthode non autorisée (GET au lieu de POST)**
```json
{
  "status": 405,
  "message": "Method Not Allowed"
}
```

---

### `POST` get_visiteur.php — Informations d'un visiteur

Retourne les informations détaillées d'un visiteur à partir de son matricule.

**Requête**
```json
{
  "matricule": "084795A"
}
```

**Réponse 200 — Succès**
```json
{
  "status": 200,
  "data": {
    "vis_matricule": "084795A",
    "vis_nom": "Smith",
    "vis_prenom": "John",
    "vis_adresse": "1 rue du Test",
    "vis_cp": "75000",
    "vis_ville": "Paris",
    "reg_code": "AL"
  }
}
```

---

## Récapitulatif des endpoints

| Endpoint | Méthode | Authentification requise | Description |
|---|---|---|---|
| `login.php` | POST | Non | Connexion utilisateur |
| `get_rap.php` | POST | Matricule | CR d'un visiteur |
| `listes.php` | GET | Non | Praticiens + médicaments |
| `get_cr.php` | GET | Non | Tous les CR |
| `insert_cr.php` | POST | Matricule | Créer un CR |
| `get_visiteur.php` | POST | Matricule | Infos d'un visiteur |

---

*THOMAS Paul — BTS SIO SLAM — Session 2026*
