# Symfony Quiz Platform - SignLearn

## 📋 Description
**Plateforme de quiz éducative inclusive** développée avec Symfony, conçue pour faciliter l'apprentissage des personnes malentendantes. L'application offre une gestion complète de quiz avec intégration d'images via API Google et une interface adaptée aux besoins spécifiques.

## 🎯 Fonctionnalités Principales

### 👥 Gestion des Rôles
- **Administrateur** : Supervision complète de la plateforme
- **Formateur** : Création, modification et gestion des quiz
- **Élève** : Passage de quiz et consultation des résultats

### ✨ Fonctionnalités Techniques
- ✅ **CRUD complet** des quiz et exercices
- ✅ **API Google Images** pour illustrations des questions
- ✅ **Interface accessible** avec support visuel amélioré
- ✅ **Système d'évaluation** automatique
- ✅ **Multi-langue** (fichiers de traduction inclus)
- ✅ **Base de données** MySQL avec Doctrine ORM
- ✅ **Templates Twig** modulaires et responsives

## 🏗️ Architecture Technique
symfony-quiz-platform/
├── src/
│ ├── Controller/ # Contrôleurs Symfony
│ ├── Entity/ # Entités Doctrine (Quiz, Exercice, User)
│ ├── Repository/ # Repositories personnalisés
│ ├── Service/ # Services métier (GoogleImageService, QuizService)
│ └── Form/ # Formulaires Symfony
├── templates/ # Templates Twig (FXML équivalent web)
│ ├── quiz/ # Pages de gestion des quiz
│ ├── exercise/ # Pages des exercices
│ └── security/ # Authentification
├── config/ # Configuration Symfony
├── translations/ # Fichiers de traduction
├── public/ # Assets et point d'entrée
└── tests/ # Tests unitaires

## 🚀 Installation

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- MySQL 5.7+
- Symfony CLI (recommandé)
- 
1. **Cloner le dépôt**
```bash
git clone https://github.com/aniiiisss123/symfony-quiz-platform.git
## 👤 Author
**Anis Saidi** - [GitHub](https://github.com/aniiiisss123)cd symfony-quiz-platform
