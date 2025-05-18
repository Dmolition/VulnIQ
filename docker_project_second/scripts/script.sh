#!/bin/bash

TARGET=$1   # Target IP passed as the argument

# Running Nmap Scan
echo "Running Nmap scan on $TARGET..."
nmap -sS -T4 -A "$TARGET" > /results/nmap_results.txt

# Running Nikto Scan
echo "Running Nikto scan on $TARGET..."
nikto -h "$TARGET" > /results/nikto_results.txt

# Running Nessus Scan (If applicable via CLI/API)
echo "Running Nessus scan on $TARGET..."
nessus -q -x -T html -o /results/nessus_results.html -H "$TARGET"

# Combining Results
echo "Combining results..."
cat /results/nmap_results.txt /results/nikto_results.txt /results/nessus_results.html > /results/combined_results.txt

echo "Scan completed! Results are in /results/combined_results.txt"
