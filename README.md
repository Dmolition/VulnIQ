# 🔐 Vulniq – AI-Powered Vulnerability & Risk Management Platform

Vulniq is a modular, AI-driven platform designed to streamline vulnerability scanning, risk analysis, and security reporting. Built with Laravel, Flask, MySQL, Docker, and integrated with Ollama using LLaMA 3 LLM, Vulniq consolidates outputs from leading security tools into a single dashboard and generates risk intelligence reports tailored for technical and executive audiences.

---

## 🧰 Key Features

### 🔎 Integrated Scanning Tools
- **Nmap** – Network and port scanning
- **Nikto** – Web application vulnerability scanning
- **Nessus** – Enterprise-grade vulnerability scanning

### 🧠 AI-Powered Risk Reports
- Leverages **Ollama with LLaMA 3** to generate natural language risk assessments.
- Summarizes findings with business impact and recommended actions.
- Produces C-level executive summaries for board and top management.

### 📊 Unified Security Dashboard
- Web dashboard built using **Laravel + JavaScript** frontend.
- Centralizes scan data, risk levels, system summaries, and compliance metrics.
- Custom filters, severity charts, and time-based reports.

### 🗂️ Multi-Format Reporting
- Export results in:
  - PDF
  - CSV
  - JSON
  - HTML

### 🐳 Containerized Architecture
- Easy to deploy with `docker-compose`
- Modular services:
  - Laravel backend
  - Flask-based AI service
  - Ollama server
  - MySQL database
  - Integrated security tools

---
## 🏗️ Architecture

```txt
                          +-----------------------------+
                          |      Laravel (API/UI)       |
                          +-----------------------------+
                                     |
             +-----------------------+------------------------+
             |                       |                        |
      +-------------+        +---------------+        +---------------+
      |    MySQL    |        | Flask (AI API)|        | Ollama (LLM)  |
      +-------------+        +---------------+        +---------------+
             |
             +----------------+
             | Scanner Engine |
             +----------------+
             | Nmap / Nikto / |
             | Nessus         |
             +----------------+

## 🏗️ Tech Stack

| Layer            | Technology         |
|------------------|--------------------|
| Backend          | Laravel (PHP)      |
| AI Microservice  | Flask (Python)     |
| Language Model   | Ollama + LLaMA 3   |
| Frontend         | JavaScript (Vue or React recommended) |
| Database         | MySQL              |
| Scanners         | Nmap, Nikto, Nessus|
| Containerization | Docker + Docker Compose |

---

## ⚙️ Installation & Setup

> 💡 **Prerequisites:** Ensure Docker and Docker Compose are installed on your system.

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/vulniq.git
cd vulniq
