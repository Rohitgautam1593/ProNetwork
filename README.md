# ProNetwork 🚀

ProNetwork is a professional networking platform inspired by LinkedIn, built with a custom PHP MVC framework. It features a modern, responsive UI and a powerful administrative dashboard.

## ✨ Key Features

### 👤 User Experience
- **Dynamic Feed**: Real-time post interaction with likes and comments.
- **Messaging**: Professional messaging system for networking.
- **Profile Management**: Customizable profiles with experience and education tracking.
- **Job Board**: Integrated job listings with "Easy Apply" functionality.
- **Notifications**: Real-time alerts for platform activities.

### 🛡️ Administrative Panel
- **Real-time Analytics**: Live dashboard metrics for platform growth.
- **User Moderation**: Full CRUD operations for user accounts and role management.
- **Content Moderation**: Flagged reports system for post and user review.
- **Entity Management**: Control over platform Companies and Job Listings.
- **Security**: Built-in safeguards against accidental deletions and unauthorized access.

## 🛠️ Tech Stack
- **Backend**: PHP (Custom MVC Architecture)
- **Database**: MySQL (PDO for secure queries)
- **Frontend**: Vanilla Javascript, CSS (Tailwind inspired), HTML5
- **Icons**: Google Material Symbols

## 🚀 Quick Start

### Prerequisites
- XAMPP / WAMP / LAMP or any PHP/MySQL environment.
- PHP 7.4+

### Installation
1. **Clone the repository**:
   ```bash
   git clone https://github.com/Rohitgautam1593/ProNetwork.git
   ```
2. **Database Setup**:
   - Create a database named `pronetwork`.
   - Import the `database/pronetwork.sql` file (if provided) or use the migration scripts.
3. **Configuration**:
   - Rename `app/config/config.php.example` to `app/config/config.php`.
   - Update the `DB_PASS` and `URLROOT` in `config.php` to match your local setup.
4. **Run**:
   - Point your local server to the `public/` directory.

## 📄 License
Distributed under the MIT License. See `LICENSE` for more information.

---
Built with ❤️ by [Rohit Gautam](https://github.com/Rohitgautam1593)
