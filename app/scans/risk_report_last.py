import pandas as pd
from langchain_ollama import ChatOllama
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas
from datetime import datetime
import os

# Define the base path to the shared Windows folder via WSL
base_path = "/mnt/d/senior_project/app/scans"

# Build full input file path (adjust filename as needed)
input_file = os.path.join(base_path, "scan_report_20250205_220059.csv")

# Load the scan report
df = pd.read_csv(input_file)
df.columns = df.columns.str.strip()

# Extract insights
risk_counts = df['Risk'].value_counts().to_dict()
top_plugins = df['Name'].value_counts().head(5).to_dict()
top_hosts = df['Host'].value_counts().head(5).to_dict()

# Build prompt
prompt = f"""
You are a cybersecurity analyst.

Summarize the key risks from a vulnerability scan report for top management. Include:
- Main risk areas
- Most vulnerable systems or services
- Recommended actions

Scan Summary:
- Risk levels: {risk_counts}
- Top vulnerabilities: {top_plugins}
- Most affected hosts: {top_hosts}
"""

# Run Ollama
model = ChatOllama(model="llama3.1:8b")
response = model.invoke(prompt)
response_content = response.content

# Create timestamped output file names
timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
txt_filename = os.path.join(base_path, f"risk_summary_report_{timestamp}.txt")
pdf_filename = os.path.join(base_path, f"risk_summary_report_{timestamp}.pdf")

# Save to TXT
with open(txt_filename, "w") as txt_file:
    txt_file.write("📋 Risk Summary Report:\n\n")
    txt_file.write(response_content)

# Save to PDF
def save_as_pdf(content, filename):
    c = canvas.Canvas(filename, pagesize=letter)
    width, height = letter
    c.setFont("Helvetica-Bold", 12)
    c.drawString(100, height - 50, "📋 Risk Summary Report:")
    c.setFont("Helvetica", 10)

    y_position = height - 70
    for line in content.splitlines():
        c.drawString(100, y_position, line)
        y_position -= 14
        if y_position < 50:
            c.showPage()
            c.set

