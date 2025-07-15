# Veterinary Records Management with AI-Powered Diagnosis Assistant

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)

> Advanced veterinary management system with AI-assisted diagnostics developed for Bulan Veterinary Clinic

<!-- ![VetAI Assistant Screenshot](https://via.placeholder.com/800x500?text=VetAI+Assistant+Screenshot) -->

## Features

### 🧠 AI-Powered Diagnosis
- Intelligent symptom analysis and diagnostic suggestions
- Breed-specific medical history evaluation
- Confidence scoring for potential conditions

### 📁 Comprehensive Records Management
- Centralized digital medical histories
- Treatment and vaccination tracking
- Lab results storage

### 📅 Smart Scheduling
- Automated appointment management
- Email reminders for pet owners
- Follow-up notification system

### 💬 Live Communication
- Real-time chat between pet owners and staff
- Consultation notes and file sharing
- Treatment plan discussions

## How It Works

1. **Pet Registration & Records**  
   Create digital profiles with medical history and owner information
   
2. **Appointment Scheduling**  
   Book and manage visits with automated reminders
   
3. **AI-Assisted Diagnosis**  
   System analyzes symptoms and suggests diagnoses
   
4. **Treatment & Follow-up**  
   Create plans, track progress, and schedule follow-ups

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Tailwind CSS, Blade Templates
- **Database**: MySQL
- **AI Engine**: Python Machine Learning (TensorFlow/Keras)
<!-- - **Deployment**: Docker, AWS EC2 -->

## Research Background
Developed as part of the research paper **"Veterinary Records Management with AI-Powered Diagnosis Assistant"** at Sorsogon State University. The system addresses key challenges in veterinary care:
- Digital transformation of paper-based records
- AI-assisted diagnostic accuracy
- Streamlined clinic operations
- Secure cloud-based data management

## Installation

### Prerequisites
- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js 16+

### Setup Steps:
```bash
1. git clone https://github.com/GitKristoff/vrm.git
2. cd vrm
3. composer install
4. npm install && npm run dev
5. cp .env.example .env
6. php artisan key:generate
7. Configure .env with your database credentials
8. php artisan serve
