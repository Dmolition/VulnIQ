from flask import Flask, send_file
from reportlab.lib.pagesizes import letter
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle
from reportlab.lib.styles import getSampleStyleSheet
from reportlab.lib import colors
from io import BytesIO
import csv
import requests  # To interact with Ollama API

app = Flask(__name__)

CSV_FILE_PATH = 'D:\\senior_project\\public\\admin\\scans\\scan_report_20250205_220059.csv'
OLLAMA_API_URL = 'http://localhost:11434/api/generate'  # Modified to use the local tunnel port
OLLAMA_MODEL = 'llama3.1:8.1'  # Replace with the model you want to use

def get_ollama_response(prompt):
    """Sends a prompt to the Ollama API and returns the response."""
    try:
        response = requests.post(
            OLLAMA_API_URL,
            json={"prompt": prompt, "stream": False, "model": OLLAMA_MODEL} # Ensure model is included
        )
        response.raise_for_status()  # Raise an exception for HTTP errors
        return response.json().get('response', '')
    except requests.exceptions.RequestException as e:
        print(f"Error communicating with Ollama: {e}")
        return ""

def analyze_for_nist(data):
    """Analyzes scan data for NIST compliance and generates recommendations using Ollama."""
    nist_report = []
    for row in data:
        vulnerability_description = row.get("Description", "")
        vulnerability_name = row.get("Name", "")
        vulnerability_risk = row.get("Risk", "")

        # Placeholder for initial NIST mapping (can be rule-based or simple keyword matching)
        nist_mapping = "Potentially related to: [Analyze Description]"

        # Craft a prompt for Ollama to get mitigation and NIST recommendations
        prompt = f"""You are a security expert providing mitigation strategies and NIST compliance recommendations for a security vulnerability.

        Vulnerability Name: {vulnerability_name}
        Description: {vulnerability_description}
        Risk Level: {vulnerability_risk}
        Potential NIST Mapping: {nist_mapping}

        Provide:
        1. A concise mitigation strategy to address this vulnerability.
        2. Relevant NIST 800-53 controls or best practices that apply to this vulnerability."""

        ollama_response = get_ollama_response(prompt)
        mitigation = ""
        nist_recommendations = ""

        if ollama_response:
            # Basic parsing of Ollama's response (you might need more sophisticated parsing)
            if "Mitigation Strategy:" in ollama_response:
                mitigation_start = ollama_response.find("Mitigation Strategy:") + len("Mitigation Strategy:")
                nist_start = ollama_response.find("NIST 800-53 controls or best practices:")
                if nist_start != -1:
                    mitigation = ollama_response[mitigation_start:nist_start].strip()
                    nist_recommendations = ollama_response[nist_start + len("NIST 800-53 controls or best practices:"):].strip()
                else:
                    mitigation = ollama_response[mitigation_start:].strip()
            elif "NIST 800-53 controls or best practices:" in ollama_response:
                nist_start = ollama_response.find("NIST 800-53 controls or best practices:") + len("NIST 800-53 controls or best practices:")
                mitigation_start = ollama_response.find("Mitigation Strategy:") # Check if mitigation was mentioned later
                if mitigation_start != -1:
                    mitigation = ollama_response[mitigation_start + len("Mitigation Strategy:"):nist_start].strip()
                nist_recommendations = ollama_response[nist_start:].strip()
            else:
                # If the format isn't as expected, just take the whole response
                nist_recommendations = ollama_response.strip()

        nist_report.append({**row, "nist_mapping": nist_mapping, "risk_nist": vulnerability_risk,
                            "mitigation": mitigation, "nist_recommendations": nist_recommendations})
    return nist_report

def generate_nist_risk_report_pdf(nist_analysis_results):
    buffer = BytesIO()
    doc = SimpleDocTemplate(buffer, pagesize=letter)
    styles = getSampleStyleSheet()
    story = []

    story.append(Paragraph("NIST Compliance Risk Management Report", styles['h1']))
    story.append(Spacer(1, 12))

    story.append(Paragraph("Executive Summary (NIST Compliance)", styles['h2']))
    # ... AI-generated summary based on NIST findings (can be another Ollama call) ...
    story.append(Spacer(1, 12))

    story.append(Paragraph("Detailed Findings and NIST Recommendations:", styles['h2']))
    data = [["Plugin ID", "Name", "Risk", "NIST Mapping", "Mitigation", "NIST Recommendations"]]
    for item in nist_analysis_results:
        data.append([
            item.get("Plugin ID", "N/A"),
            item.get("Name", "N/A"),
            item.get("Risk", "N/A"),
            item.get("nist_mapping", "N/A"),
            item.get("mitigation", "N/A"),
            item.get("nist_recommendations", "N/A")
        ])

    table = Table(data)
    table.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
        ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
        ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
        ('GRID', (0, 0), (-1, -1), 1, colors.black)
    ]))
    story.append(table)
    story.append(Spacer(1, 12))

    story.append(Paragraph("Recommendations (NIST Compliance)", styles['h2']))
    for item in nist_analysis_results:
        story.append(Paragraph(f"<b>Plugin ID:</b> {item.get('Plugin ID', 'N/A')}", styles['Normal']))
        story.append(Paragraph(f"<b>Name:</b> {item.get('Name', 'N/A')}", styles['Normal']))
        story.append(Paragraph(f"<b>Mitigation:</b> {item.get('mitigation', 'N/A')}", styles['Normal']))
        story.append(Paragraph(f"<b>NIST Recommendations:</b> {item.get('nist_recommendations', 'N/A')}", styles['Normal']))
        story.append(Spacer(1, 6))

    doc.build(story)
    buffer.seek(0)
    return buffer

@app.route('/generate_nist_report')
def generate_nist_report():
    data = []
    try:
        with open(CSV_FILE_PATH, 'r', newline='') as csvfile:
            reader = csv.DictReader(csvfile)
            for row in reader:
                data.append(row)
    except FileNotFoundError:
        return "Error: Scan report file not found."

    nist_analysis_results = analyze_for_nist(data)

    pdf_buffer = generate_nist_risk_report_pdf(nist_analysis_results)
    return send_file(
        pdf_buffer,
        mimetype='application/pdf',
        as_attachment=True,
        download_name='nist_risk_management_report.pdf'
    )

@app.route('/')
def index():
    return "<h1>Vulnerability Report Generator</h1><p><a href='/generate_report'>Generate Basic Report</a></p><p><a href='/generate_nist_report'>Generate NIST Risk Management Report</a></p>"

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=4000)