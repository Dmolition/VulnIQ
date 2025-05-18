FROM ubuntu:20.04

# Install dependencies for Nmap, Nikto, and Nessus
RUN apt-get update && \
    apt-get install -y nmap nikto wget && \
    wget https://www.tenable.com/downloads/api/v1/public/pages/nessus/downloads/10076/download?i_redirect=1 -O nessus.deb && \
    dpkg -i nessus.deb && \
    apt-get install -f -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copy script.sh into the container and make it executable
COPY ./scripts /scripts
RUN chmod +x /scripts/script.sh

# Set the working directory
WORKDIR /scripts

# Set the ENTRYPOINT to run the script
ENTRYPOINT ["/bin/bash", "/scripts/script.sh"]
