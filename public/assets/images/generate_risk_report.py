import pandas as pd
from langchain_ollama import ChatOllama

# Load the scan report
df = pd.read_csv(r"D:\senior_project\public\assets\images\scan_report_20250205_220059.csv")

# Basic analysis
severity_counts = df['Severity'].value_counts().to_dict()
top_components = df['Component'].value_counts().head(5).to_dict()
top_vulns = df['Vulnerability Type'].value_counts().head(5).to_dict()

# Prompt
prompt = f"""
You are a cybersecurity analyst.

Summarize the key risks from a vulnerability scan report in a way that is easy to understand for top management. Include:
- Main risk areas
- Most vulnerable components
- Suggested actions

Scan Summary:
- Severity counts: {severity_counts}
- Top affected components: {top_components}
- Common vulnerability types: {top_vulns}
"""

# Chat model
model = ChatOllama(model="llama3.1:8b")
response = model.invoke(prompt)

# Show result
print("\n📋 Risk Summary Report:\n")
print(response.content)
