# Amar Bangla Academy - LMS Environment & Deployment Guide

This project is configured to run **seamlessly on both Local PC (XAMPP / Laragon / artisan serve) and cPanel Shared Hosting without requiring environment modifications.**

---

## 🚀 Running on Local PC

### Option A: Using XAMPP Apache (Direct Browser Access)
Because of the built-in root `.htaccess` and root `index.php` gateway, you can access the project directly in your browser:
```
http://localhost/Amar%20Bangla%20Academy/
```
or
```
http://localhost/Amar%20Bangla%20Academy/public/
```
No virtual host configuration is required!

### Option B: Using Artisan Serve
Run in your terminal:
```bash
php artisan serve
```
Then visit: `http://127.0.0.1:8000`

---

## 🌐 Deploying to cPanel Shared Hosting

### Method 1: Uploading directly into `public_html` (Easiest)
1. Zip the entire project folder (`Amar Bangla Academy`).
2. In cPanel **File Manager**, upload and extract all files directly into `public_html/`.
3. That's it! The root `.htaccess` and `index.php` automatically route traffic to the `public/` directory without exposing internal project files.

### Method 2: Recommended cPanel Directory Structure
If you prefer keeping core files outside `public_html`:
1. Create a folder in your cPanel home directory (e.g. `/home/username/amar-bangla`).
2. Upload all project files there.
3. Move the contents of `/public/` into `/public_html/`.
4. Update `public_html/index.php` to point to `/../amar-bangla/vendor/autoload.php` and `bootstrap/app.php`.

---

## 🗄️ Database (Zero-Config SQLite vs MySQL)

- **Default Out-of-the-Box:** Uses **SQLite** (`database/database.sqlite`). It works immediately on both local PC and cPanel with zero setup, zero database user creation, and zero configuration!
- **Switching to MySQL (Optional):**
  In `.env`:
  ```ini
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=your_db_name
  DB_USERNAME=your_db_user
  DB_PASSWORD=your_db_password
  ```
  Then run:
  ```bash
  php artisan migrate --seed
  ```

---

## 📦 Zero Build Step (Frontend)
- Uses modern **Tailwind CSS**, **Alpine.js**, **FontAwesome 6**, and **Google Fonts (Hind Siliguri + Inter)**.
- **No `npm run build` is required on cPanel!** The application loads with full responsiveness, interactive accordions, and checkout modals on any shared hosting environment.

---

## 🎓 LMS Features Included
1. **Course Showcase & Flagship Page:** Learning outcomes (Goals), Curriculum breakdown with modules, lecture duration, free previews.
2. **Instructors & Mentors:** Industry profiles, bios, ratings, and social links.
3. **Live Classrooms:** Zoom and Google Meet integration with live schedules, meeting IDs, and passcodes.
4. **Online Exams & Quizzes:** Timed module quizzes, practical assessments, and verifiable digital certificates.
5. **Online Payment:** Integrated checkout with bKash (বিকাশ), Nagad (নগদ), Rocket (রকেট), and Cards.
6. **24/7 Dedicated Support System:** Daily Google Meet doubt-clearing sessions, Discord/Telegram forum, code review.
