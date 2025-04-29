import pandas as pd
import subprocess
from langchain_ollama import ChatOllama
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas

# Define the paths
local_csv = "/mnt/d/senior_project/app/scans/scan_report_20250205_220059.csv"
remote_dir = "/scratch-beegfs/datasets/202100657/ollama/"
remote_csv = f"{remote_dir}scan_report_20250205_220059.csv"

# Define SSH credentials (use a password-based connection)
user = "202100657"
host = "hayrat.uob.edu.bh"
password = "DzIh21T4"

# Upload CSV to the remote cluster via SCP
print("📤 Uploading CSV...")
subprocess.run([
    "sshpass", "-p", password,
    "scp", "-o", "StrictHostKeyChecking=no",  # Automatically accept new host keys
    local_csv,
    f"{user}@{host}:{remote_dir}/"
], check=True)

# Load the scan report from the remote cluster (file is now uploaded)
df = pd.read_csv(remote_csv)

# Strip whitespace from column names (good habit)
df.columns = df.columns.str.strip()

# Use actual columns from your file
risk_counts = df['Risk'].value_counts().to_dict()
top_plugins = df['Name'].value_counts().head(5).to_dict()
top_hosts = df['Host'].value_counts().head(5).to_dict()

# Build the prompt for LangChain
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

# Set up LLaMA (using ChatOllama)
model = ChatOllama(model="llama3.1:8b")
response = model.invoke(prompt)  # Get the response content from LLaMA model
response_content = response.content

# 1. Save the result to a TXT file
with open("risk_summary_report.txt", "w") as txt_file:
    txt_file.write("📋 Risk Summary Report:\n")
    txt_file.write("\n")
    txt_file.write(response_content)

# 2. Save the result to a PDF file
def save_as_pdf(content):
    pdf_file = "risk_summary_report.pdf"
    c = canvas.Canvas(pdf_file, pagesize=letter)
    width, height = letter  # Page size
    c.setFont("Helvetica", 10)

    # Add title
    c.drawString(100, height - 50, "📋 Risk Summary Report:")
    y_position = height - 70  # Starting position for text

    # Add content to PDF
    for line in content.splitlines():
        c.drawString(100, y_position, line)
        y_position -= 14  # Move to next line in PDF

        if y_position < 50:  # Prevents content from going off the page
            c.showPage()  # Create a new page
            c.setFont("Helvetica", 10)
            y_position = height - 50

    # Save the PDF file
    c.save()

# Save the response content to PDF
save_as_pdf(response_content)


