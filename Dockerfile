FROM python:3.10-slim

LABEL maintainer="ton.nom@email.com" \
      version="0.1" \
      description="GreenIT - Analyse de l'impact écologique de projets logiciels en Python"

WORKDIR /app

COPY . .

CMD ["python", "main.py"]

#test
