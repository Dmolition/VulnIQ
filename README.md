# 🔐 VulnIQ – AI-Powered Vulnerability & Risk Management Platform

Vulniq is a modular, AI-driven platform designed to streamline vulnerability scanning, risk analysis, and security reporting. Built with Laravel, Flask, MySQL, Docker, Nginx, and integrated with Ollama using LLaMA 3 LLM, Vulniq consolidates outputs from leading security tools into a single dashboard and generates risk intelligence reports tailored for technical and executive audiences.

---

## 🏗️ Architecture

```txt
                          +-----------------------------+
                          |         AdminLTE            |
                          +-----------------------------+
                                     |
                          +-----------------------------+
                          |      Laravel (API/UI)       |
                          +-----------------------------+
                                     |
         +----------------+----------+-------------+----------------+
         |                |                        |                |
  +-------------+  +---------------+        +---------------+  +----------------------------+
  |    MySQL    |  | Flask (AI API)|        | Ollama (LLM)  |  |   Docker (Nginx + Tools)   |
  +-------------+  +---------------+        +---------------+  +----------------------------+
                                                                  |
                                                           +----------------+
                                                           | Scanner Engine |
                                                           +----------------+
                                                           | Nmap / Nikto / |
                                                           | Nessus         |
                                                           +----------------+

```
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
  - **Nginx reverse proxy** – Handles internal routing from Laravel to scanner containers, ensuring clean and consistent communication between services.

---


## 🏗️ Tech Stack

| Layer            | Technology                             |
|------------------|--------------------------------------|
| Backend          | Laravel (PHP)                        |
| AI Microservice  | Flask (Python)                      |
| Language Model   | Ollama + LLaMA 3                    |
| Frontend         | JavaScript (Vue or React recommended) |
| Database         | MySQL                              |
| Scanners         | Nmap, Nikto, Nessus                |
| Reverse Proxy    | Nginx (within Docker for scanners)|
| Containerization | Docker + Docker Compose            |


---

## ⚙️ Installation & Setup

> 💡 **Prerequisites:** Run the following commands to install all required tools on a Debian/Ubuntu-based system. Use equivalents for macOS or Windows as needed.

### 🧰 Install System Dependencies

```bash
# Install PHP and Composer
sudo apt update && sudo apt install php php-cli php-mbstring unzip curl php-xml php-bcmath php-curl php-mysql git -y
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Python 3 and pip
sudo apt install python3 python3-pip python3-venv -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
sudo usermod -aG docker $USER

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.24.4/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Install Nmap
sudo apt install nmap -y

# Install Nikto
sudo apt install nikto -y

# Install Nessus
# Note: Nessus installation requires downloading the package from Tenable and installing it manually.
# Below is a basic example for Debian/Ubuntu. Adjust version and URL as needed.

wget https://downloads.nessus.org/nessus3dl.php?file=nessus-10.6.1-debian6_amd64.deb&licence_accept=yes&t=YOURTOKEN -O nessus.deb
sudo dpkg -i nessus.deb
sudo systemctl start nessusd
sudo systemctl enable nessusd

```

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/vulniq.git
cd vulniq
