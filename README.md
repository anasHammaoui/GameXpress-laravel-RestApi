
# 📦 API Administrateur E-commerce (GameXpress)

## 🚀 Introduction
Cette API d'administration pour une plateforme e-commerce (GameXpress) est développée avec **Laravel 11**. Elle constitue la première phase d'un backend qui s'étend sur **trois semaines**.

## 🛠️ Technologies Utilisées
- **Framework** : Laravel 11 && PHP 8.3
- **Authentification** : Laravel Sanctum
- **Gestion des rôles et permissions** : Spatie Permission
- **Tests** : Pest PHP ou unitTest
- **Base de données** : MySQL
- **Documentation API** : Swagger/OpenAPI (bonus)

## 📐 Architecture
L'API suit une architecture **RESTful** avec :
- 📌 **Versionnement** : `v1`
- ✅ **Structure de réponse cohérente**
- 🔐 **Authentification par token** (Sanctum)
- 🛡️ **Gestion des permissions** avec Spatie

---

## 🔗 Endpoints Principaux

### 🔑 1. Authentification Administrateur
- 🔹 **Inscription** : `POST /api/v1/admin/register`
- 🔹 **Connexion** : `POST /api/v1/admin/login`
- 🔹 **Déconnexion** : `POST /api/v1/admin/logout`

### 📊 2. Tableau de Bord
- 📈 **Statistiques** : `GET /api/v1/admin/dashboard`
- 🏷️ **Notification** :  Je souhaite recevoir des notifications par email pour les stocks critiques.

### 🛍️ 3. Gestion des Produits
- 📜 **Lister** : `GET /api/v1/admin/products`
- 🔍 **Voir un produit** : `GET /api/v1/admin/products/{id}`
- ➕ **Créer** : `POST /api/v1/admin/products`
- ✏️ **Modifier** : `PUT /api/v1/admin/products/{id}`
- ❌ **Supprimer** : `DELETE /api/v1/admin/products/{id}`

### 🗂️ 4. Gestion des Catégories
- 📜 **Lister** : `GET /api/v1/admin/categories`
- ➕ **Créer** : `POST /api/v1/admin/categories`
- ✏️ **Modifier** : `PUT /api/v1/admin/categories/{id}`
- ❌ **Supprimer** : `DELETE /api/v1/admin/categories/{id}`

### 👥 5. Gestion des Utilisateurs
- 📜 **Lister** : `GET /api/v1/admin/users`
- ➕ **Créer** : `POST /api/v1/admin/users`
- ✏️ **Modifier** : `PUT /api/v1/admin/users/{id}`
- ❌ **Supprimer** : `DELETE /api/v1/admin/users/{id}`

---

## 🗄️ Modèles de Données

### 👤 1. Utilisateur (`users`)
| Champ              | Type         | Description |
|--------------------|-------------|-------------|
| `id`              | int         | Identifiant unique |
| `name`            | string      | Nom de l'utilisateur |
| `email`           | string      | Adresse e-mail |
| `password`        | string      | Mot de passe |
| `email_verified_at` | timestamp | Vérification e-mail |
| `remember_token`  | string      | Jeton de session |
| `timestamps`      | timestamp   | Dates de création et mise à jour |
| `deleted_at`      | timestamp   | Suppression (soft delete) |

### 🏷️ 2. Catégorie (`categories`)
| Champ       | Type     | Description |
|------------|---------|-------------|
| `id`       | int     | Identifiant unique |
| `name`     | string  | Nom de la catégorie |
| `slug`     | string  | Identifiant URL-friendly |
| `parent_id` | int    | Catégorie parente (si applicable) |
| `timestamps` | timestamp | Dates de création et mise à jour |

### 🏷️ 3. Produit (`products`)
| Champ      | Type     | Description |
|------------|---------|-------------|
| `id`       | int     | Identifiant unique |
| `name`     | string  | Nom du produit |
| `slug`     | string  | Identifiant URL-friendly |
| `price`    | decimal | Prix du produit |
| `stock`    | int     | Quantité en stock |
| `status`   | string  | État du produit (disponible, en rupture) |
| `category_id` | int  | Catégorie associée |
| `timestamps` | timestamp | Dates de création et mise à jour |
| `deleted_at` | timestamp | Suppression (soft delete) |

### 🖼️ 4. Image Produit (`product_images`)
| Champ       | Type     | Description |
|------------|---------|-------------|
| `id`       | int     | Identifiant unique |
| `product_id` | int   | Produit associé |
| `image_url` | string | Lien de l'image |
| `is_primary` | bool  | Image principale (true/false) |
| `timestamps` | timestamp | Dates de création et mise à jour |

---

## 🛡️ Gestion des Rôles et Permissions

### 🎭 Rôles
- 👑 `super_admin`
- 🛍️ `product_manager`
- 👥 `user_manager`

### 🔑 Permissions
- 📊 `view_dashboard`
- 🛍️ `view_products`, `create_products`, `edit_products`, `delete_products`
- 🗂️ `view_categories`, `create_categories`, `edit_categories`, `delete_categories`
- 👥 `view_users`, `create_users`, `edit_users`, `delete_users`

---

## 🧪 Plan de Tests
- ✅ Tests **unitaires** pour chaque endpoint
- ✅ Tests **de validation** des rôles et permissions
- ✅ Tests **de performance** sur les endpoints critiques

---

## 📂 Organisation du Code
```
📂 app
 ├── 📁 Http
 │   ├── 📂 Controllers
 │   │   └── 📂 Api/V1/Admin
 │   ├── 📂 Requests
 │   ├── 📂 Resources
 ├── 📁 Models
 ├── 📁 Middleware
 ├── 📂 routes
 │   ├── api.php
 ├── 📂 tests
 │   ├── Feature/Api/V1/Admin
```

---

## 📅 Planning de Développement (Semaine 1)

### 📆 **Jour 1**
✅ Initialisation du projet Laravel 11  
✅ Configuration de **Sanctum** et **Spatie**  
✅ Mise en place de la **structure API**  

### 📆 **Jour 2**
✅ Implémentation de l'**authentification** (`register`, `login`, `logout`)  
✅ Configuration des **rôles et permissions**  
✅ Développement du **tableau de bord**  

### 📆 **Jour 3**
✅ Développement des **endpoints produits**  
✅ Écriture des **tests unitaires**  

### 📆 **Jour 4**
✅ Développement des **endpoints catégories **  
✅ Écriture des **tests unitaires**  

### 📆 **Jour 5**
✅ Développement des **endpoints catégories & utilisateurs**  
✅ Finalisation des **tests et documentation API**  

### 📤 Exporter les Endpoints
1. Ouvrez **Postman**
2. Sélectionnez la **collection** contenant vos endpoints
3. Cliquez sur les trois points **(...)** puis sur **Exporter**
4. Choisissez le format **JSON** et cliquez sur **Exporter**
5. Enregistrez le fichier pour le partager ou le rendue