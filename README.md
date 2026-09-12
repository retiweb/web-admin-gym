# 🏋️ Gym & Fitness Management System

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![Livewire](https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=black)](https://alpinejs.dev/)
[![Postgresql](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org/)

A modern, full-stack Web Application designed to streamline gym daily operations, manage memberships, track transactions, and handle member/walk-in guest check-ins in real-time.

> **Note for Recruiters:** This project serves as my hands-on deep dive into **Laravel Livewire**, demonstrating how to build dynamic, single-page-like interfaces (SPA feeling) entirely within PHP without the overhead of heavy JavaScript frameworks.

---

## 📸 Preview

| Dashboard & Transactions | Interactive Check-In Modal |
| :---: | :---: |
| ![Dashboard Screenshot](https://via.placeholder.com/600x350?text=Dashboard+UI+Preview) | ![Modal Screenshot](https://via.placeholder.com/600x350?text=Detail+Modal+Preview) |
*(Ganti link gambar di atas dengan screenshot asli aplikasi Anda)*

---

## ✨ Key Features

- 🎫 **Smart Check-In System:** Quick check-in for both registered **Gym Members** and **Daily Pass (Walk-in Guests)** with access code matching.
- 💳 **Transaction Tracking:** Clear breakdown of package purchases, total revenue calculations, and receipt details.
- 🗂️ **Dynamic Component Modals:** Native HTML `<dialog>` element integrated seamlessly with Livewire events (`$dispatch`) and Alpine.js for smooth UI rendering.
- 🔍 **Real-time Search & Filtering:** Instant data lookup without full page reloads using Livewire reactive state.
- 📊 **Membership Package Management:** Assign and update membership durations and status dynamically.

---

## 🛠️ Tech Stack & Architecture

- **Backend Framework:** Laravel 11
- **Reactivity & State:** Livewire 3 (Volt / Class Components) + Alpine.js
- **Styling & UI:** Tailwind CSS
- **Database:** MySQL (Structured with Eager Loading to prevent N+1 queries)
- **Modal Architecture:** HTML5 Native `<dialog>` controlled via event-driven Alpine `@event.window` listener.

---

## 💡 Key Engineering Insights & Learnings

Building this application with Livewire provided valuable insights into modern Laravel workflows:

1. **Event-Driven Component Interactivity:**
   Implmenting dynamic modal windows using Livewire's `$dispatch` to pass ID parameters directly into child components without re-rendering the whole page.
2. **Database Optimization:**
   Utilizing Laravel Eloquent relationships (`CheckIn::with(['member', 'transaction'])`) to optimize query counts during data fetching.
3. **Hybrid Frontend Patterns:**
   Combining native browser APIs (HTML5 `<dialog>`) with Alpine.js for instantaneous UI responses, keeping state synchronization clean on the server via Livewire.

---

## 🚀 Getting Started Locally

### Prerequisites
- PHP `>= 8.2`
- Composer
- Node.js & NPM
- Postgresql Database

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone [https://github.com/username/gym-management-livewire.git](https://github.com/username/gym-management-livewire.git)
   cd gym-management-livewire
