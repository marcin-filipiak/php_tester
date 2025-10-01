<center>
<img src="assets/gfx/art.jpg" height="200px">
</center>

# 🎓 TESTER – Open Source Student Test Management System

**TESTER** is a lightweight, open-source web application designed to support teachers and students in creating, managing, and evaluating tests in a school environment. Built with PHP, it offers a clean separation of logic through controllers and a simple interface for test handling, class management, and grading – all without external frameworks.

---

## ✨ Features

- 🧠 **Test creation** with:
  - Multiple-choice questions,
  - Open-ended questions (with manual grading),
  - AI-assisted generation (e.g., via ChatGPT).
- 👩‍🏫 **Teacher Panel**:
  - Create and edit tests,
  - Manage classes and users,
  - Grade open questions,
  - Export/import tests and questions in JSON format.
- 🧑‍🎓 **Student Panel**:
  - Take assigned tests,
  - View scores and feedback,
  - Review test history.
- 📊 **Scoring system**:
  - Automatic evaluation of closed questions,
  - Automatic grading of open questions,
  - Average calculation per student/test/class.
- 🧩 Role-based access (student/teacher).
- 🔐 Simple login & session management.
- 🇵🇱 Polish-language interface (easily customizable).

---

## 🛠️ Tech Stack

- PHP 8+
- HTML/CSS frontend
- JSON-based import/export
- Custom MVC-like structure (no frameworks)

---

## 📂 Project Structure

```
index.php                 # Main router
/controllers/             # Controllers for actions
/models/                  # Data models
/views/                   # HTML templates
/includes/                # Helpers, Database class
/public/                  # Media used in tests
/assets/                  # CSS and gfx used by system
/config/                  # Config files
```

---

## 📦 Getting Started

1. Clone the repository:
   ```bash
   git clone https://github.com/marcin-filipiak/TESTER.git
   cd TESTER
   ```

2. Configure database connection in `config/Config.php`.

3. Import the SQL schema (you can create your own or request a sample schema).

4. Start a local server:
   ```bash
   php -S localhost:8000
   ```

5. Open `http://localhost:8000` in your browser.

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).  
© 2025 Marcin Filipiak ([GitHub Profile](https://github.com/marcin-filipiak))

---

## 🙋‍♂️ Author

**Marcin Filipiak**  
🔗 GitHub: [@marcin-filipiak](https://github.com/marcin-filipiak)

---

## 🤝 Contributions

Pull requests, suggestions, and improvements are welcome! Let's make education smarter together.
