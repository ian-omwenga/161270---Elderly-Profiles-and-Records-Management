# Elderly Care Records Management Platform

An integrated web platform designed for home-based elderly care monitoring. This system allows family members and caregivers to manage elder profiles, log daily care visits, track health indicators, and automatically monitor patient well-being using AI. 

ElderCare features a dedicated Python Flask microservice that utilizes the **VADER (Valence Aware Dictionary and Sentiment Reasoner)** lexicon to perform sentiment analysis on unstructured caregiver visit notes, automatically flagging negative health observations and triggering alerts on the family dashboard.

## 📁 Project Structure
The repository is divided into two main architectural components:
*   `/` (Root): The main Laravel web application and PostgreSQL database migrations.
*   `/flask-sentiment-service`: The Python-based AI microservice for VADER sentiment analysis.

## ⚙️ Prerequisites
To run this project locally, you will need:
*   PHP >= 8.1 & Composer
*   PostgreSQL
*   Python >= 3.9 & pip
*   Node.js & npm

## 🚀 Installation & Setup

### 1. Web Application (Laravel / PHP)
Navigate to the root directory of the project and run the following commands:
```bash
# Install PHP dependencies
composer install

# Install frontend dependencies
npm install && npm run build

# Set up environment variables
cp .env.example .env
